# MapTool

Ferramenta interna de **análise geográfica de concorrência** para consultoria de
marketing, processos e tecnologia. Mapeia o estabelecimento do cliente e seus
concorrentes, calcula distâncias internamente e gera relatórios em PDF.

## Stack

PHP 8.3 · Laravel 13 · SQLite · Blade + Tailwind · Google Maps API · dompdf

## Requisitos

- PHP 8.3+ com extensões: `openssl`, `pdo_sqlite`, `sqlite3`, `mbstring`,
  `fileinfo`, `curl`, `gd`, `zip`, `intl`
- Composer 2+
- Node 20+ e npm

## Setup local

```bash
# 1. Dependências
composer install
npm install

# 2. Ambiente
cp .env.example .env
php artisan key:generate
# Edite o .env e defina GOOGLE_MAPS_API_KEY e SEED_SOCIO_PASSWORD

# 3. Banco (SQLite) + contas dos sócios
php artisan migrate
php artisan db:seed --class=SociosSeeder

# 4. Assets + servidor
npm run dev          # terminal 1 (assets em watch)
php artisan serve    # terminal 2 → http://127.0.0.1:8000
```

Acesse `http://127.0.0.1:8000` e faça login com uma conta semeada
(ex.: `edieworm@gmail.com`). **Não há registro público** — contas são criadas
pelo `SociosSeeder` (edite `database/seeders/SociosSeeder.php` para adicionar sócios).

## Documentação

- [CLAUDE.md](CLAUDE.md) — arquitetura e decisões técnicas
- [SESSIONS.md](SESSIONS.md) — histórico de sessões
- [docs/deploy-oci.md](docs/deploy-oci.md) — deploy na Oracle Cloud (OCI, 1GB RAM)

## Cálculo de distância

As distâncias entre concorrentes e o estabelecimento do cliente são calculadas
**internamente** pela fórmula de Haversine (`app/Services/CalculadoraDistancia.php`),
sem usar a API paga do Google.
