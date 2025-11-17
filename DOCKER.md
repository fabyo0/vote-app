# Docker Setup for Vote App

This project is fully dockerized and ready to run with Docker and Docker Compose.

## Prerequisites

- Docker
- Docker Compose

## Services

The Docker setup includes the following services:

- **app**: Laravel application (PHP 8.3 with FPM and Nginx)
- **mysql**: MySQL 8.0 database
- **redis**: Redis cache server
- **mailhog**: Email testing tool

## Quick Start

### 1. Copy environment file

```bash
cp .env.docker .env
```

Or modify your existing `.env` file with these Docker-specific settings:

```env
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=vote_app
DB_USERNAME=root
DB_PASSWORD=secret

REDIS_HOST=redis
MAIL_HOST=mailhog
MAIL_PORT=1025
```

### 2. Build and start containers

```bash
docker-compose up -d --build
```

### 3. Access the application

- **Application**: http://localhost:8080
- **MailHog UI**: http://localhost:8025

## Useful Commands

### Start containers
```bash
docker-compose up -d
```

### Stop containers
```bash
docker-compose down
```

### View logs
```bash
docker-compose logs -f app
```

### Run artisan commands
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan tinker
```

### Access MySQL
```bash
docker-compose exec mysql mysql -u root -psecret vote_app
```

### Access application shell
```bash
docker-compose exec app bash
```

### Rebuild containers
```bash
docker-compose down
docker-compose up -d --build
```

### Clear all data and start fresh
```bash
docker-compose down -v
docker-compose up -d --build
```

## Port Mappings

- **8080**: Laravel application
- **3307**: MySQL database (mapped from 3306)
- **6380**: Redis (mapped from 6379)
- **1025**: MailHog SMTP
- **8025**: MailHog Web UI

## Database Connection

From your host machine, you can connect to MySQL using:

- **Host**: localhost
- **Port**: 3307
- **Database**: vote_app
- **Username**: root
- **Password**: secret

## Troubleshooting

### Container won't start
```bash
docker-compose logs app
```

### Database connection issues
Make sure MySQL container is fully started:
```bash
docker-compose ps
```

### Permission issues
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Clear caches
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

## Development Workflow

1. Make code changes on your host machine
2. Changes are automatically reflected in the container (via volume mount)
3. For configuration changes, restart the container:
   ```bash
   docker-compose restart app
   ```

## Production Deployment

For production, you should:

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Use strong passwords
3. Remove volume mounts and rebuild the image
4. Use a reverse proxy (like Traefik or Nginx) in front of the app
5. Set up SSL/TLS certificates
6. Use managed database services instead of containerized databases