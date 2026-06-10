# SESSIONS.md — Histórico de Sessões

> Registro enxuto do que foi decidido/feito em cada sessão. Apenas o que importa
> para retomar o contexto depois. Detalhes técnicos vivos ficam no [CLAUDE.md](CLAUDE.md).

---

## Sessão 04 — 2026-06-09 — Gestão de Pontos + recálculo de distâncias

**Objetivo**: implementar o núcleo da ferramenta — estabelecimento + concorrentes
por análise, com cálculo de distância funcionando.

**Decisão de produto (com o Duilso):** a `GOOGLE_MAPS_API_KEY` está **vazia** no `.env`.
Optamos por **manual-first**: construir toda a Gestão de Pontos com coordenadas
digitadas à mão (lat/lng) e distância calculada de verdade agora; o **mapa interativo
e o Places Autocomplete entram numa próxima etapa**, quando a key existir — sem retrabalho.

**Feito:**
- **CRUD de Pontos** aninhado na Análise: `PontoController` (resource **shallow**,
  sem `index`/`show`), `PontoRequest` (valida lat/lng com `between`), `PontoFactory`
  com states `estabelecimento()`/`concorrente()`. Rotas: `create`/`store` sob
  `analises/{analise}/pontos`; `edit`/`update`/`destroy` sob `pontos/{ponto}`.
- **Regra de negócio**: cada análise tem **no máximo 1 estabelecimento** (validado no
  store + UX esconde o botão quando já existe). O `tipo` não muda na edição.
- **Recálculo automático** via `CalculadoraDistancia` injetado no controller, disparado
  ao criar/editar/remover pontos. Ajuste no serviço: **sem estabelecimento, as distâncias
  dos concorrentes são zeradas para `null`** (não exibe valor obsoleto).
- **Tela da Análise** ganhou seções **Estabelecimento** e **Concorrentes** (tabela com
  distância formatada). Cada ponto tem link "abrir no Google Maps" (`maps?q=lat,lng`) —
  funciona **sem API key**.
- **Testes**: `PontoTest` (13 testes: CRUD, estabelecimento único, recálculo ao mover,
  zera ao remover estabelecimento, validações). Suíte: **48 → 60 verdes** (157 asserções).

**Pendente para a próxima sessão (precisa da key do Google):**
- **Mapa interativo** plotando estabelecimento + concorrentes na tela da análise.
- **Places Autocomplete** no form de ponto para preencher nome/endereço/lat/lng.
- Depois: upload de anexos por ponto, geração do PDF da análise, deploy OCI.

**Como retomar:**
```bash
git pull
php artisan migrate
php artisan serve         # http://127.0.0.1:8000 → login com edieworm@gmail.com
php artisan test          # 60 verdes ao fim da sessão 04
```
Fluxo: **/clientes → cliente → Nova análise → abrir a análise → Definir estabelecimento
e Adicionar concorrente** (coordenadas no formato "lat, lng" do Google Maps).

---

## Sessão 03 — 2026-06-09 — CRUD de Análises + limpeza de órfãos

**Objetivo**: validar o projeto, fechar gaps e implementar o próximo CRUD do roadmap.

**Feito:**
- **CRUD de Análises** completo, aninhado no cliente: `AnaliseController` (resource
  **shallow**, sem `index`), `AnaliseRequest`, `AnaliseFactory`, views Blade
  (create/edit/show + partials `form` e `status-badge`). Rotas: `create`/`store`
  sob `clientes/{cliente}/analises`; `show`/`edit`/`update`/`destroy` sob `analises/{analise}`.
- **Status como fonte única**: constantes `STATUS_*` + mapa `STATUSES` no model `Analise`,
  usados na validação (`Rule::in`) e na exibição (`statusLabel()` + badge colorido).
- **Lista de análises embutida no show do cliente** (em vez de uma página `index`
  redundante): `ClienteController@show` carrega `analises` com `withCount('pontos')`.
- **Gap fechado**: `CalculadoraDistancia` (Haversine) estava **sem teste e sem ser
  chamado**. Criada `PontoFactory` e `CalculadoraDistanciaTest` (cálculo conhecido,
  pontos coincidentes, `recalcularAnalise` persistindo distância, e caso sem estabelecimento).
- **Limpeza**: removidos os órfãos do Breeze `RegisteredUserController` e
  `auth/register.blade.php` (sem rota apontando para eles).
- **Testes**: suíte passou de **33 → 48 verdes** (126 asserções).

**Pendente para a próxima sessão:**
- Próximo no roadmap: **Gestão de Pontos** (estabelecimento + concorrentes) com mapa
  interativo (Google Maps JS) e busca via Places Autocomplete. Ao salvar pontos,
  disparar `CalculadoraDistancia::recalcularAnalise()` (o serviço já está testado e pronto).
- `welcome.blade.php` (scaffolding padrão, nunca renderizado pois `/` redireciona) ainda
  existe — referência a `route('register')` é protegida por `Route::has`, então é inofensivo.

**Como retomar:**
```bash
git pull
php artisan migrate
php artisan serve         # http://127.0.0.1:8000 → login com edieworm@gmail.com
npm run dev
php artisan test          # 48 verdes ao fim da sessão 03
```
Fluxo: **/clientes → abrir um cliente → "Nova análise"** (card de Análises no show do cliente).

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

**Como retomar:**
```bash
git pull                  # traz o que foi commitado (commit 69954e5 = CRUD de Clientes)
php artisan migrate       # garante o schema atualizado
php artisan serve         # http://127.0.0.1:8000 → login com edieworm@gmail.com
npm run dev               # assets em watch
php artisan test          # suíte completa (33 verdes ao fim da sessão 02)
```
A área de Clientes fica em **/clientes** (link "Clientes" no menu superior).

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
