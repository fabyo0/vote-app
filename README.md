# 🚀 Vote App - Idea Voting Platform

[![CI Tests](https://github.com/fabyo0/vote-app/actions/workflows/ci.yml/badge.svg)](https://github.com/fabyo0/vote-app/actions/workflows/ci.yml)
[![Run tests](https://github.com/fabyo0/vote-app/actions/workflows/laravel.yml/badge.svg)](https://github.com/fabyo0/vote-app/actions/workflows/laravel.yml)
[![codecov](https://codecov.io/github/fabyo0/vote-app/graph/badge.svg?token=YXZ7XMNABM)](https://codecov.io/github/fabyo0/vote-app)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=flat)](https://livewire.laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A modern, full-featured voting platform built with Laravel 10 and Livewire. Submit ideas, vote, engage in threaded discussions, and receive real-time notifications - all in a beautiful, responsive interface.

---

## 📸 Screenshots

<details>
<summary>Click to view screenshots</summary>

### Home Page
<img width="1411" height="633" alt="Screenshot 2025-11-19 at 20 09 11" src="https://github.com/user-attachments/assets/293fae26-3965-45c3-b3b9-54e1bb11e477" />

### Idea Details & Comments
<img width="1236" height="628" alt="Screenshot 2025-11-19 at 20 09 46" src="https://github.com/user-attachments/assets/0427262a-0415-443e-99ed-339dfdf9b492" />

### User Profile
<img width="1159" height="650" alt="Screenshot 2025-11-19 at 20 10 18" src="https://github.com/user-attachments/assets/e806c018-5a76-4ac3-b612-83a3d2de9c40" />

### Idea Comments
<img width="742" height="429" alt="Screenshot 2025-11-19 at 20 11 26" src="https://github.com/user-attachments/assets/7a1c301d-13ba-4087-b95e-b44d90e0c79a" />

### Notification Settings
<img width="1102" height="588" alt="Screenshot 2025-11-19 at 20 12 08" src="https://github.com/user-attachments/assets/1deba621-bf0e-48a3-b393-55c9df199bb4" />

</details>

---

## ✨ Key Features

### 🗳️ Core Functionality
- **Idea Management** - Submit, edit, and categorize ideas with status tracking
- **Smart Voting System** - Upvote/downvote with duplicate prevention
- **Unlimited Nested Comments** - Full threaded discussions with infinite nesting
- **Status Workflow** - Track ideas through: Open → Considering → In Progress → Implemented → Closed

### 👥 Social Features
- **User Profiles** - Customizable profiles with activity tracking
- **Follow System** - Follow users and see their contributions
- **Avatar System** - Auto-generated avatars (Laravolt) with upload support (Spatie Media Library)
- **Username URLs** - Clean, SEO-friendly URLs like `/users/@username`

### 🔔 Advanced Notifications
- **Dual Delivery System**:
  - 📡 **Real-time** - Instant updates via Pusher WebSockets
  - 💾 **Database Polling** - Fallback for environments without WebSockets
  - 🔄 **Hybrid Mode** - Best of both worlds (recommended)
- **Notification Types**: New votes, comments, status changes, mentions
- **Admin Control Panel** - Switch delivery methods on-the-fly

### 🔐 Authentication & Security
- **Social Login** - OAuth integration with Facebook & Google
- **CSRF Protection** - Built-in Laravel security
- **XSS Prevention** - Automatic output escaping
- **Role-Based Access** - Admin/User permissions

### 🎨 User Experience
- **Dark Mode** - Toggle between light/dark themes
- **Responsive Design** - Mobile-first approach
- **Real-time Updates** - Live UI updates without page refresh
- **Spam Prevention** - Rate limiting and duplicate detection

### 📊 Additional Features
- **Polls** - Attach polls to ideas for community input
- **Search & Filters** - Find ideas by status, category, or popularity
- **Activity Feed** - Track all platform activity
- **Email Notifications** - Digest emails for important updates

---

## 🎯 Demo
> **Live Demo:** [https://vote-app.on-forge.com/](https://vote-app.on-forge.com/)

**Test Credentials:**
```
Email: demo@demo.com
Password: Pa$$w0rd!
```

---

## 📋 Requirements

Ensure the following are installed:

| Requirement | Version | Required |
|------------|---------|----------|
| PHP | >= 8.0 | ✅ |
| Composer | 2.x | ✅ |
| MySQL / PostgreSQL | 5.7+ / 12+ | ✅ |
| Node.js | >= 16.x | ✅ |
| npm | >= 8.x | ✅ |
| Redis | Latest | ⭕ Optional |
| Pusher Account | - | ⭕ Optional |

---

## 🚀 Quick Start

### 1️⃣ Clone & Install
```bash
# Clone repository
git clone https://github.com/fabyo0/vote-app.git
cd vote-app

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate
```

### 2️⃣ Database Setup

Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vote_app
DB_USERNAME=root
DB_PASSWORD=your_password
```

Run migrations and seed data:
```bash
php artisan migrate --seed
php artisan storage:link
```

### 3️⃣ Queue Configuration

Set up queue for background jobs:
```env
QUEUE_CONNECTION=database
```

Create jobs table:
```bash
php artisan queue:table
php artisan migrate
```

### 4️⃣ Build & Run
```bash
# Build frontend assets
npm run build

# Start development server
php artisan serve

# In separate terminal - Start queue worker
php artisan queue:work
```

Visit: **http://localhost:8000** 🎉

---

## ⚙️ Configuration

### Notification System

Choose your notification delivery method:

#### Option 1: Database Only (Default)
No additional setup required. Polls database every 30 seconds.

#### Option 2: Real-time with Pusher

1. Create account at [pusher.com](https://pusher.com)
2. Update `.env`:
```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=your-cluster

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

3. Clear cache and rebuild:
```bash
php artisan config:clear
npm run build
```

#### Admin Control

Navigate to `/admin/settings` to switch between:
- 💾 Database Only
- 📡 Pusher Only  
- 🔄 Both (Recommended)

### Social Authentication (Optional)

1. **Create OAuth Apps**:
   - [Facebook Developers](https://developers.facebook.com)
   - [Google Cloud Console](https://console.cloud.google.com)

2. **Update `.env`**:
```env
FACEBOOK_CLIENT_ID=your-id
FACEBOOK_CLIENT_SECRET=your-secret

GOOGLE_CLIENT_ID=your-id
GOOGLE_CLIENT_SECRET=your-secret
```

3. **Set Callback URLs**:
   - Facebook: `http://yourdomain.com/auth/facebook/callback`
   - Google: `http://yourdomain.com/auth/google/callback`

---

## 🛠️ Development Commands
```bash
# Start all services
php artisan serve             # Laravel server (port 8000)
npm run dev                   # Vite HMR server
php artisan queue:work        # Queue worker

# Database
php artisan migrate:fresh --seed   # Fresh start with data
php artisan db:seed                # Re-seed only

# Livewire
php artisan make:livewire ComponentName
php artisan livewire:publish --assets

# Cache management
php artisan optimize:clear         # Clear all caches
php artisan config:cache           # Cache config
php artisan route:cache            # Cache routes
php artisan view:cache             # Cache views
```

---

## 🧪 Testing

Run test suite:
```bash
# All tests
php artisan test

# Specific suites
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# With coverage
php artisan test --coverage
php artisan test --coverage-html coverage
```

Configure test database in `.env`:
```env
DB_DATABASE_TESTING=vote_app_test
```

---

## 🏭 Production Deployment

### Environment Setup

Update `.env` for production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Performance optimization
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Mail configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

### Optimization
```bash
# Install production dependencies
composer install --optimize-autoloader --no-dev

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Build assets
npm run build
```

### Queue Worker (Supervisor)

Create `/etc/supervisor/conf.d/vote-app-worker.conf`:
```ini
[program:vote-app-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start vote-app-worker:*
```

---

## 🚨 Troubleshooting

### Clear All Caches
```bash
php artisan optimize:clear
```

### Permission Issues (Linux/macOS)
```bash
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Queue Not Processing
```bash
# Check failed jobs
php artisan queue:failed

# Retry all
php artisan queue:retry all

# Monitor queue
php artisan queue:work --verbose
```

### Pusher Not Working
1. Check browser console (F12) for errors
2. Verify credentials in `.env`
3. Check [Pusher Dashboard](https://dashboard.pusher.com) for activity
4. Clear config: `php artisan config:clear`
5. Rebuild assets: `npm run build`

### Frontend Assets 404
```bash
npm run build
php artisan storage:link
```

---

## 📚 Tech Stack

### Backend
- **Laravel 10** - PHP Framework
- **Livewire 3** - Full-stack framework
- **MySQL/PostgreSQL** - Database
- **Redis** - Caching & Queues

### Frontend
- **Alpine.js** - Lightweight JS framework
- **Tailwind CSS** - Utility-first CSS
- **Vite** - Build tool

### Packages
- **Spatie Media Library** - File uploads
- **Laravolt Avatar** - Avatar generation
- **Laravel Socialite** - OAuth
- **Pusher** - WebSockets

---

## 📖 Project Structure
```
app/
├── Http/
│   ├── Controllers/        # HTTP Controllers
│   └── Livewire/          # Livewire Components
├── Models/                # Eloquent Models
├── Notifications/         # Notification Classes
└── Providers/             # Service Providers

resources/
├── views/
│   ├── livewire/         # Livewire Views
│   └── layouts/          # Layout Templates
└── js/                   # Frontend Assets

database/
├── migrations/           # Database Schema
├── seeders/             # Data Seeders
└── factories/           # Model Factories

tests/
├── Feature/             # Feature Tests
└── Unit/                # Unit Tests
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. **Fork** the repository
2. **Create** a feature branch
```bash
   git checkout -b feature/amazing-feature
```
3. **Commit** your changes (use [Conventional Commits](https://conventionalcommits.org))
```bash
   git commit -m "feat: add amazing feature"
```
4. **Push** to your branch
```bash
   git push origin feature/amazing-feature
```
5. **Open** a Pull Request

### Commit Convention
- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation
- `style:` Formatting
- `refactor:` Code restructuring
- `test:` Adding tests
- `chore:` Maintenance

---

## 🐛 Issues & Support

- **Bug Reports**: [Open an issue](https://github.com/fabyo0/vote-app/issues/new?template=bug_report.md)
- **Feature Requests**: [Request a feature](https://github.com/fabyo0/vote-app/issues/new?template=feature_request.md)
- **Discussions**: [GitHub Discussions](https://github.com/fabyo0/vote-app/discussions)

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Livewire](https://livewire.laravel.com) - Full-stack Framework
- [Tailwind CSS](https://tailwindcss.com) - CSS Framework
- [Pusher](https://pusher.com) - Real-time Infrastructure
- [Spatie](https://spatie.be) - Amazing Laravel Packages

---

## 📞 Connect

- **GitHub**: [@fabyo0](https://github.com/fabyo0)
- **Issues**: [Report here](https://github.com/fabyo0/vote-app/issues)
- **Discussions**: [Join here](https://github.com/fabyo0/vote-app/discussions)

---

<div align="center">

### ⭐ Star this repo if you find it helpful!

**Built with ❤️ using Laravel & Livewire**

[Report Bug](https://github.com/fabyo0/vote-app/issues) · [Request Feature](https://github.com/fabyo0/vote-app/issues) · [Documentation](https://github.com/fabyo0/vote-app/wiki)

</div>
