# SocialManager — Base Project

A clean Laravel 12 starter base project equipped with:
- **Authentication**: Phone + OTP mobile login via API (Sanctum) with multi-device management & device session revocation.
- **Admin Panel**: Tabler-based dashboard, User management (All, Blocked, Soft-deleted), Profile management.
- **DataTables**: Yajra DataTables integration for User listing.
- **API Documentation**: L5-Swagger / OpenAPI 3.0 setup with pre-configured Auth endpoints.
- **Docker Compose**: Containerized environment configured for FrankenPHP/Octane, MySQL 8.0, Redis 7, and phpMyAdmin.

---

## Docker Setup

### Containers & Ports
- **App (FrankenPHP/Octane)**: `socialmanager_app` — Port `8201`
- **Queue Worker**: `socialmanager_queue`
- **MySQL**: `socialmanager_mysql` — Port `3308` (Database: `socialmanager`)
- **Redis**: `socialmanager_redis` — Port `6381`
- **phpMyAdmin**: `socialmanager_phpmyadmin` — Port `8082`

### Start Containers
```bash
docker compose up -d --build
```

---

## Local Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan l5-swagger:generate
php artisan serve
```

### Default Admin Credentials
- **Email**: `admin@socialmanager.com`
- **Password**: `password123`
