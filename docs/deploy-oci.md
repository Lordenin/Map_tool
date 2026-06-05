# Deploy na Oracle Cloud (OCI) — instância de 1GB de RAM

Guia de deploy **nativo** (nginx + php-fpm), otimizado para a instância
*Always Free* mais básica da OCI (VM.Standard.E2.1.Micro, 1 vCPU / 1GB RAM).

> Decisão: **sem Docker**. O overhead de containers consome RAM preciosa em 1GB.
> Stack nativa é mais leve e previsível aqui.

## Visão geral

```
Internet ──▶ nginx (porta 80/443) ──▶ php-fpm (unix socket) ──▶ Laravel
                                              │
                                              └─▶ SQLite (arquivo)
```

## 1. Provisionamento da instância

- SO recomendado: **Ubuntu 22.04 LTS** (ou Oracle Linux 8, ajustando os pacotes).
- Na OCI, libere as portas no **Security List / Network Security Group**:
  - Ingress `80/tcp` e `443/tcp` (HTTP/HTTPS).
- Mantenha apenas `22/tcp` (SSH) restrito ao seu IP, se possível.

> Importante: além do firewall da OCI, o Ubuntu na OCI vem com `iptables` ativo.
> Libere as portas também no SO:
> ```bash
> sudo iptables -I INPUT 6 -m state --state NEW -p tcp --dport 80 -j ACCEPT
> sudo iptables -I INPUT 6 -m state --state NEW -p tcp --dport 443 -j ACCEPT
> sudo netfilter-persistent save
> ```

## 2. Swap (essencial em 1GB)

Composer e o build podem estourar a RAM. Crie 2GB de swap:

```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

## 3. Pacotes

```bash
sudo apt update
sudo apt install -y nginx git unzip \
  php8.3-fpm php8.3-cli php8.3-sqlite3 php8.3-mbstring \
  php8.3-xml php8.3-curl php8.3-gd php8.3-zip php8.3-intl php8.3-bcmath

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node (para o build dos assets) — pode buildar localmente e subir /public/build
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt install -y nodejs
```

## 4. Deploy do código

```bash
cd /var/www
sudo git clone <URL_DO_REPO> maptool
sudo chown -R $USER:www-data maptool
cd maptool

composer install --no-dev --optimize-autoloader
npm ci && npm run build      # ou suba /public/build buildado localmente

cp .env.example .env
php artisan key:generate
# Edite o .env: APP_ENV=production, APP_DEBUG=false, APP_URL, GOOGLE_MAPS_API_KEY, SEED_SOCIO_PASSWORD

touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --class=SociosSeeder --force
php artisan storage:link

# Caches de produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Permissões

```bash
sudo chown -R www-data:www-data storage bootstrap/cache database
sudo chmod -R 775 storage bootstrap/cache
sudo chmod 664 database/database.sqlite
sudo chmod 775 database
```

> O processo `www-data` precisa escrever no **arquivo** SQLite **e no diretório**
> `database/` (SQLite cria arquivos temporários `-wal`/`-shm` ao lado do banco).

## 5. php-fpm tunado para 1GB

Edite `/etc/php/8.3/fpm/pool.d/www.conf`:

```ini
; "ondemand" sobe workers só quando há requisição — economiza RAM em idle.
pm = ondemand
pm.max_children = 6
pm.process_idle_timeout = 10s
pm.max_requests = 500
```

OPcache em `/etc/php/8.3/fpm/php.ini` (ou um `.ini` em `conf.d`):

```ini
opcache.enable=1
opcache.memory_consumption=64
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0   ; produção: invalide manualmente no deploy
```

```bash
sudo systemctl restart php8.3-fpm
```

## 6. nginx

`/etc/nginx/sites-available/maptool`:

```nginx
server {
    listen 80;
    server_name SEU_DOMINIO_OU_IP;
    root /var/www/maptool/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }

    client_max_body_size 20M;   # uploads de anexos/imagens
}
```

```bash
sudo ln -s /etc/nginx/sites-available/maptool /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl restart nginx
```

## 7. HTTPS (opcional, recomendado)

Com domínio apontando para o IP da instância:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d SEU_DOMINIO
```

## 8. Atualizações futuras (deploy incremental)

```bash
cd /var/www/maptool
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
sudo systemctl reload php8.3-fpm
```

## Backup

SQLite = um arquivo. Backup é copiar `database/database.sqlite` (e `storage/app`
para os anexos). Ex.: cron diário com `sqlite3 database/database.sqlite ".backup ..."`.
