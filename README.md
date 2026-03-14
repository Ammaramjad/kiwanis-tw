# 基扶社管理系統 (Kiwanis-Style Club Management System)

A full-stack web application for Taiwan Kiwanis-style nonprofit association management.

## 技術架構 (Tech Stack)

- **Backend**: Laravel 12, PHP 8.3+
- **Database**: MySQL 8
- **Admin Panel**: Filament 3
- **Frontend**: Tailwind CSS, Livewire
- **Auth**: Laravel Breeze (member portal) + Filament (admin)
- **API**: Laravel Sanctum (REST API)
- **Queue**: Redis + Laravel Queue
- **Permissions**: Spatie Laravel Permission
- **Communication**: LINE Messaging API, Email

## 功能模組 (Modules)

1. **會員管理** — Member management with profiles, QR member cards
2. **社團管理** — Club management with officer tracking
3. **地區管理** — District hierarchy management
4. **活動管理** — Event management with registration & QR check-in
5. **公告系統** — Announcements with LINE/email notifications
6. **文件中心** — Document library with access control
7. **通訊記錄** — Contact logging (call/SMS/LINE/email)
8. **入會申請** — Public membership application workflow
9. **REST API** — Sanctum-authenticated API for mobile

## 系統角色 (Roles)

| 角色 | 說明 |
|------|------|
| super_admin | 完整系統存取 |
| hq_admin | 總部管理員 |
| district_admin | 地區管理員 |
| club_admin | 社團管理員 |
| club_officer | 社團幹部 |
| member | 一般會員 |
| guest | 訪客 |

## 部署指南 (Deployment — Ubuntu + Nginx + MySQL + PHP-FPM)

### 1. 系統需求

```bash
# PHP 8.3 + extensions
sudo apt-get install -y php8.3-fpm php8.3-mysql php8.3-redis php8.3-gd \
  php8.3-zip php8.3-mbstring php8.3-xml php8.3-curl php8.3-intl php8.3-bcmath

# MySQL 8
sudo apt-get install -y mysql-server

# Redis
sudo apt-get install -y redis-server

# Node.js 20+
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. 資料庫設定

```sql
CREATE DATABASE kiwanis_tw CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kiwanis'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON kiwanis_tw.* TO 'kiwanis'@'localhost';
FLUSH PRIVILEGES;
```

### 3. 應用程式設定

```bash
# Clone & install
cd /var/www
git clone https://github.com/Ammaramjad/kiwanis-tw.git
cd kiwanis-tw

composer install --no-dev --optimize-autoloader
npm install && npm run build

cp .env.example .env
# Edit .env: set DB_*, APP_KEY, MAIL_*, LINE_*, etc.
php artisan key:generate

# Storage
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Migrations & seed
php artisan migrate --force
php artisan db:seed --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Nginx 設定

```nginx
server {
    listen 80;
    server_name kiwanis.tw www.kiwanis.tw;
    root /var/www/kiwanis-tw/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/kiwanis-tw /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

### 5. Queue Worker (Supervisor)

```ini
; /etc/supervisor/conf.d/kiwanis-queue.conf
[program:kiwanis-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/kiwanis-tw/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/kiwanis-tw/storage/logs/queue.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start kiwanis-queue:*
```

### 6. Schedule (Cron)

```bash
* * * * * cd /var/www/kiwanis-tw && php artisan schedule:run >> /dev/null 2>&1
```

### 7. SSL (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d kiwanis.tw -d www.kiwanis.tw
```

## 管理員入口 (Admin Panel)

- URL: `/admin`
- 預設帳號: `admin@kiwanis.tw` / `password` (seed data)

## 會員入口 (Member Portal)

- URL: `/dashboard` (需登入)
- 預設帳號: `member@kiwanis.tw` / `password` (seed data)

## API 文件 (API)

Base URL: `/api`

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login` | 登入 |
| POST | `/api/auth/register` | 註冊 |
| POST | `/api/auth/logout` | 登出 (需驗證) |
| GET | `/api/auth/me` | 當前使用者 |
| GET | `/api/members` | 會員列表 |
| GET | `/api/members/{id}` | 會員詳情 |
| GET | `/api/events` | 活動列表 |
| POST | `/api/events/{id}/register` | 報名活動 |
| DELETE | `/api/events/{id}/unregister` | 取消報名 |
| GET | `/api/announcements` | 公告列表 |

## LINE Messaging API 設定

1. 在 LINE Developers Console 建立 Messaging API channel
2. 將 `LINE_CHANNEL_ACCESS_TOKEN` 填入 `.env`
3. 廣播公告時系統會自動透過 `SendAnnouncementNotifications` Job 發送

## 開發環境 (Local Development)

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# Update .env with local DB settings
php artisan migrate --seed
npm run dev
php artisan serve
```

## License

MIT
