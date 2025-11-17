# QLXM - Hệ thống Quản Lý Xe Máy

Dự án quản lý xe máy bao gồm Backend API và Frontend Admin/Client.

## Cấu trúc dự án

```
QLXM/
├── be-qlxm/          # Backend API (Laravel)
└── fe-qlxm/          # Frontend (Laravel Blade)
```

## Backend (be-qlxm)

### Cài đặt
```bash
cd be-qlxm
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### Chạy server
```bash
php artisan serve
```

## Frontend (fe-qlxm)

### Cài đặt
```bash
cd fe-qlxm
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Chạy server
```bash
php artisan serve --port=8001
```

## Công nghệ sử dụng

- Laravel 12.x
- MySQL / SQLite
- JavaScript (Vanilla)
- Bootstrap 5

## Deployment

- Backend: Heroku (https://be-qlxm-9b1bc6070adf.herokuapp.com)
- Frontend: Local Development

## License

MIT License
