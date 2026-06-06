# CLAUDE.md — MapTool

> Memória técnica do projeto. Pontos cruciais de arquitetura e decisões.
> Mantido atualizado a cada mudança estrutural. Histórico de sessões fica em [SESSIONS.md](SESSIONS.md).

## Visão

Ferramenta interna de **análise geográfica de concorrência** para a consultoria.
Permite mapear o estabelecimento de um cliente e seus concorrentes, calcular
distâncias internamente, registrar observações/anexos e gerar relatórios em PDF
para apoiar o planejamento e a entrega ao cliente.

**Princípios**: simples, leve, responsiva. Sem firulas. Roda em máquina OCI de 1GB de RAM.

## Stack

| Camada      | Tecnologia                                    |
|-------------|-----------------------------------------------|
| Backend     | PHP 8.3 + Laravel 13                          |
| Frontend    | Blade + Tailwind + Alpine.js (via Breeze)     |
| Banco       | SQLite (arquivo único, leve para 1GB de RAM)  |
| Autenticação| Laravel Breeze (stack Blade), **sem registro público** |
| PDF         | barryvdh/laravel-dompdf (sem binário externo) |
| Mapa        | Google Maps JS API + Places API (só exibição/busca) |
| Distância   | Cálculo interno (Haversine) — **não usa API do Google** |

## Decisões de arquitetura (e o porquê)

- **SQLite** em vez de MySQL/Postgres: equipe pequena (sócios), máquina de 1GB.
  Zero processo de banco consumindo RAM; backup é copiar um arquivo. Migrável depois.
- **Cálculo de distância interno (Haversine)**: requisito explícito de não usar a
  Distance Matrix API do Google (custo). Precisão > 99,5% em distâncias urbanas.
  Implementado em `app/Services/CalculadoraDistancia.php`.
- **Sem mapa no PDF**: relatório foca em dados/tabelas/anexos. Evita custo da Static
  Maps API e mantém o PDF leve.
- **Sem registro público**: ferramenta interna. Contas criadas via `SociosSeeder`.
- **Anexos polimórficos**: tabela `anexos` (`anexavel_type`/`anexavel_id`) permite
  anexar a `Ponto` ou `Analise` sem retrabalho.
- **dompdf** em vez de soluções com headless browser (wkhtmltopdf/puppeteer): não
  exige binário/Chromium na OCI de 1GB.

## Modelo de dados

```
User (sócio)
 └─ Cliente (nome, segmento, observacoes)
     └─ Analise (titulo, descricao, status, data_analise)
         └─ Ponto (tipo: estabelecimento|concorrente, nome, endereco,
                   latitude, longitude, observacoes, distancia_metros)
             └─ Anexo (polimórfico: imagem|documento)
```

- Cada `Analise` tem **1** ponto `estabelecimento` (referência) e **N** `concorrente`.
- `distancia_metros` é pré-calculada (Haversine) do concorrente até o estabelecimento.
- Recalculada por `CalculadoraDistancia::recalcularAnalise()` ao alterar pontos.

## Convenções

- **Idioma pt-BR**: nomes de variáveis/colunas, comentários e documentação.
- Comentários só quando o *porquê* não é óbvio pelo código.
- Nenhuma feature/correção para `main` sem testes.
- Nunca commitar `.env`, banco SQLite ou segredos.
- Nunca deixar `dd()`, `var_dump()`, `console.log` no código commitado.

## Ambiente de desenvolvimento (Windows)

PHP, Composer e Node foram instalados via `winget`/instalador oficial.
**Atenção**: o PHP do winget fica em
`%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.3_...` e o `php.ini` foi
criado a partir do `php.ini-development` com as extensões habilitadas
(openssl, pdo_sqlite, sqlite3, mbstring, fileinfo, curl, gd, zip, exif, intl).

> Em terminais novos, o PATH pode não refletir as instalações recentes. Se `php`
> não for encontrado, recomponha o PATH a partir do registro:
> ```powershell
> $env:Path = [Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [Environment]::GetEnvironmentVariable("Path","User")
> ```

## Comandos úteis

```bash
php artisan serve              # sobe a aplicação (http://127.0.0.1:8000)
npm run dev                    # assets em modo watch (Vite)
npm run build                  # build de produção dos assets
php artisan migrate            # roda migrations
php artisan db:seed --class=SociosSeeder   # cria/garante contas dos sócios
php artisan test               # roda a suíte de testes
```

## Roadmap / próximos passos

- [x] CRUD de Clientes (carteira **compartilhada** entre sócios; `user_id` = auditoria)
- [ ] CRUD de Análises (dentro do cliente)
- [ ] Gestão de Pontos (estabelecimento + concorrentes) com mapa interativo
- [ ] Busca de locais via Places Autocomplete (preenche lat/lng)
- [ ] Recálculo automático de distâncias ao salvar pontos
- [ ] Upload de anexos (imagens/documentos) por ponto
- [ ] Geração do relatório PDF da análise
- [ ] Testes de feature para cada fluxo
- [ ] Deploy na OCI (ver [docs/deploy-oci.md](docs/deploy-oci.md))
