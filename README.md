# 🚀 Laravel Livewire Project Setup Guide

[![CI Tests](https://github.com/fabyo0/vote-app/actions/workflows/ci.yml/badge.svg)](https://github.com/fabyo0/vote-app/actions/workflows/ci.yml)
&nbsp;&nbsp;
[![Run tests](https://github.com/fabyo0/vote-app/actions/workflows/laravel.yml/badge.svg)](https://github.com/fabyo0/vote-app/actions/workflows/laravel.yml)
&nbsp;&nbsp;
[![codecov](https://codecov.io/github/fabyo0/vote-app/graph/badge.svg?token=YXZ7XMNABM)](https://codecov.io/github/fabyo0/vote-app)

## ✅ Requirements

Make sure the following are installed on your system:

- **PHP** >= 8.0  
- **Composer**  
- **MySQL** (or any other supported database)  
- **Node.js & npm** *(required for Vite and frontend assets)*
- **Redis** *(optional, for queue jobs and caching)*

---

## ⚙️ Setup Instructions

### 1. Install Dependencies

Install all PHP dependencies via Composer:

```bash
composer install
```

Install Node.js dependencies for frontend assets:

```bash
npm install
```

If the `.env` file is missing, copy it from the example:

```bash
cp .env.example .env
```

---

### 2. Configure the Database

Laravel uses **MySQL** by default. To use a different database, update `config/database.php` and your `.env` file.

#### Steps:
1. Install and configure your preferred database.
2. Create a new database for the project.
3. Update your `.env` file with the correct credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

---

### 3. Generate Application Key

```bash
php artisan key:generate
```

---

### 4. Run Migrations

Run database migrations to create all necessary tables:

```bash
php artisan migrate
```

---

### 5. Seed the Database (Optional)

To populate your database with sample data:

```bash
php artisan db:seed
```

---

### 6. File Uploads - Create Storage Symlink

To make uploaded files publicly accessible:

```bash
php artisan storage:link
```

---

### 7. Queue Configuration (Important!)

This project uses Laravel queues for background processing. Configure your queue settings in `.env`:

```env
QUEUE_CONNECTION=database
# Or use Redis for better performance:
# QUEUE_CONNECTION=redis
```

If using database queues, create the jobs table:

```bash
php artisan queue:table
php artisan migrate
```

**⚠️ Important:** You must run the queue worker to process background jobs:

```bash
php artisan queue:work
```

For production, use a process manager like Supervisor to keep the queue worker running:

```bash
# Keep this running in a separate terminal or use Supervisor
php artisan queue:work --daemon
```

### Alternative Queue Commands:
- Process a single job: `php artisan queue:work --once`
- Process specific queue: `php artisan queue:work --queue=high,default`
- Restart all queue workers: `php artisan queue:restart`

---

### 8. Frontend Assets (Vite)

Build and compile frontend assets using Vite:

#### For Development:
```bash
npm run dev
```

#### For Production:
```bash
npm run build
```

#### Watch for Changes (Development):
```bash
npm run dev -- --watch
```

---

### 9. Run the Application

#### Option 1: Using Laravel's Built-in Server

```bash
php artisan serve
```

#### Option 2: Laravel Valet (Recommended for macOS)

Place your project in the Valet directory and access it via `http://project-name.test`.

#### Option 3: Using Artisan with Custom Host/Port

```bash
php artisan serve --host=0.0.0.0 --port=8080
```

---

## 🔧 Production Setup

### Environment Configuration

Update your `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Use Redis for better performance
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Configure mail settings
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

### Optimization Commands

Run these commands for production optimization:

```bash
# Install production dependencies only
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Build optimized frontend assets
npm run build
```

---

## 🧪 Running Tests

This project includes automated tests using PHPUnit.

### Run All Tests

```bash
php artisan test
```

Or directly via PHPUnit:

```bash
./vendor/bin/phpunit
```

### Run Specific Test Types

```bash
# Run only feature tests
php artisan test --testsuite=Feature

# Run only unit tests
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Testing Database Configuration

Configure a separate database for testing in your `.env`:

```env
DB_DATABASE_TESTING=your_testing_database
```

Update `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="your_testing_database"/>
```

---

## 🚨 Troubleshooting

### Common Issues and Solutions

#### Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### Permission Issues (Linux/macOS)
```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### Queue Jobs Not Processing
- Make sure `php artisan queue:work` is running
- Check queue failed jobs: `php artisan queue:failed`
- Retry failed jobs: `php artisan queue:retry all`

#### Frontend Assets Not Loading
- Run `npm run build` for production
- Check if `public/build` directory exists
- Verify Vite configuration in `vite.config.js`

---

## 📚 Development Workflow

### Daily Development Commands

```bash
# Start development servers (run in separate terminals)
php artisan serve              # Laravel server
npm run dev                   # Vite dev server  
php artisan queue:work        # Queue worker

# When pulling updates
composer install
npm install
php artisan migrate
```

### Livewire Commands

```bash
# Create a new Livewire component
php artisan make:livewire ComponentName

# Publish Livewire assets
php artisan livewire:publish --assets
```

---

## 📝 Additional Notes

- Don't forget to set the correct `APP_URL` in your `.env`
- Keep your queue workers running in production using Supervisor
- Use Redis for better caching and queue performance in production
- Always run `npm run build` before deploying to production
- Monitor your queue jobs and failed jobs regularly

---

## 🔐 Security Notes

- Never commit your `.env` file to version control
- Use strong, unique `APP_KEY` in production
- Enable HTTPS in production
- Regularly update dependencies: `composer update` and `npm update`
- Use Laravel's built-in security features (CSRF, XSS protection, etc.)

---

Happy coding! 🧑‍💻
