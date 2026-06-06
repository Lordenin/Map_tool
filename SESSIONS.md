# SESSIONS.md — Histórico de Sessões

> Registro enxuto do que foi decidido/feito em cada sessão. Apenas o que importa
> para retomar o contexto depois. Detalhes técnicos vivos ficam no [CLAUDE.md](CLAUDE.md).

---

## Sessão 02 — 2026-06-05 — Repositório remoto + CRUD de Clientes

**Objetivo**: publicar o projeto no GitHub e implementar o primeiro CRUD.

**Feito:**
- Repositório publicado em **https://github.com/Lordenin/Map_tool** (remote `origin`,
  branch `main`). Confirmado que `.env`, banco SQLite e `vendor/` não vazaram.
- **CRUD de Clientes** completo: `ClienteController` (resource), `ClienteRequest`
  (validação), rotas `Route::resource('clientes')` sob middleware `auth`, views
  Blade (index/create/edit/show + partial de form), `ClienteFactory` e link na navegação.
- **Decisão de produto (com o Duilso)**: carteira de clientes é **compartilhada**
  entre os sócios — todos veem/editam todos. `user_id` só registra quem cadastrou.
- **Testes**: `ClienteTest` (10 testes cobrindo CRUD + visibilidade compartilhada).
- **Limpeza**: removido `RegistrationTest` (registro público não existe) e ajustado
  `ExampleTest` (raiz redireciona para login). Suíte: **33 testes verdes**.

**Pendente para a próxima sessão:**
- Resíduos órfãos do Breeze sem rota: `RegisteredUserController` e `auth/register.blade.php`
  (decidir se remove).
- Próximo no roadmap: **CRUD de Análises** (aninhado no cliente).

---

## Sessão 01 — 2026-06-04 — Fundação do projeto

**Objetivo**: preparar o terreno (ambiente, base Laravel, modelo de dados,
autenticação, documentação e versionamento).

**Decisões tomadas (com o Duilso):**
- Banco: **SQLite** (leve para OCI 1GB).
- Organização: **Cliente → Análises → Pontos** (estabelecimento + concorrentes).
- PDF: **sem imagem de mapa**, só dados/tabelas/anexos.
- Autenticação: **contas fixas**, sem registro público.
- Ambiente: **instalação nativa** (winget), sem Docker.

**Feito:**
- Instalado PHP 8.3.31, Composer 2.10.1, Node 24 LTS + npm; `php.ini` configurado
  com as extensões necessárias.
- Criado projeto **Laravel 13** com SQLite já ativo.
- Instalado **Breeze** (Blade + Tailwind) e **removido o registro público**;
  rota `/` redireciona para login/dashboard.
- Modelo de dados: migrations + models `Cliente`, `Analise`, `Ponto`, `Anexo`
  (anexos polimórficos); relação `clientes` no `User`.
- `CalculadoraDistancia` (Haversine) criado.
- `SociosSeeder` criado; conta inicial `edieworm@gmail.com` semeada.
- Instalado `barryvdh/laravel-dompdf`.
- `.env.example` com `GOOGLE_MAPS_API_KEY`, `VITE_GOOGLE_MAPS_API_KEY`,
  `SEED_SOCIO_PASSWORD`; `.gitignore` passou a ignorar o banco SQLite.
- Criados `CLAUDE.md`, `SESSIONS.md`, `README.md` e docs de deploy OCI.
- Repositório git inicializado.

**Pendente para a próxima sessão:**
- Implementar os CRUDs (Clientes → Análises → Pontos) e o mapa interativo.
- Ver o roadmap completo no [CLAUDE.md](CLAUDE.md#roadmap--próximos-passos).

**Como retomar:**
```bash
php artisan serve         # http://127.0.0.1:8000  → login com edieworm@gmail.com
npm run dev               # assets em watch
```
