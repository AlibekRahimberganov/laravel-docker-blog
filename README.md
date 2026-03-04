# Blogsite - Laravel Blog Platform with Docker

A blog application built with Laravel and Docker, featuring user authentication, post management, and category organization.

## Features

- **User Authentication**: Register, login, and user profile management
- **Post Management**: Create, read, update, and delete blog posts
- **Categories**: Organize posts by categories
- **Responsive Design**: Mobile-friendly interface
- **Database Seeding**: Pre-populated sample data
- **Docker**: Fully containerized application for easy deployment

## Requirements

- Docker & Docker Compose
- PHP 8.2+ (when running locally without Docker)
- Composer
- Node.js & npm

## Quick Start

### 1. Clone and Setup
Clone the project:
```bash
git clone git@github.com:AlibekRahimberganov/blog-site.git
```

Copy environment configuration:
```bash
cp .env.example .env
```

Install PHP dependencies:
```bash
composer install
```

Install JavaScript dependencies:
```bash
npm install
```

### 2. Database Setup

Run migrations:
```bash
php artisan migrate
```

Seed the database with sample data:
```bash
php artisan db:seed
```

### 3. Build Assets

Development build:
```bash
npm run dev
```

Production build:
```bash
npm run build
```

### 4. Run the Application

With Docker:
```bash
docker-compose up -d
```

Without Docker:
```bash
php artisan serve
```

The application will be available at `http://localhost:9001`

## Project Structure

```
├── app/
│   ├── Models/           # Database models (User, Posts, Category)
│   └── Http/Controllers/ # Application controllers
├── database/
│   ├── migrations/       # Database migrations
│   ├── seeders/          # Data seeders
│   └── factories/        # Model factories for testing
├── resources/
│   ├── views/            # Blade templates
│   ├── css/              # Stylesheets
├── routes/               # Route definitions
```

## Configuration

Key configuration files:
- `config/app.php` - Application settings
- `config/database.php` - Database connection
- `docker-compose.yaml` - Docker services configuration

## Database

The application uses the following main tables:
- **users** - User accounts
- **posts** - Blog posts
- **categories** - Post categories

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Blade, Vite
- **Database**: Sqlite
- **Containerization**: Docker & Docker Compose
- **Testing**: PHPUnit