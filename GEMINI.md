# Blogsite - Laravel Blog Platform

A containerized Laravel blog application featuring user authentication, post management, and category organization.

## Project Overview

-   **Framework**: [Laravel 12.x](https://laravel.com/docs/12.x)
-   **Runtime**: PHP 8.2-fpm
-   **Database**: SQLite (stored in `database/database.sqlite`)
-   **Frontend**: [Vite](https://vitejs.dev/), [Tailwind CSS 4.0](https://tailwindcss.com/), Blade Templates
-   **Infrastructure**: Docker & Docker Compose (Nginx + PHP-FPM)
-   **Architecture**: Standard Laravel MVC

## Key Components

-   **Models**: `User`, `Posts`, `Category` (located in `app/Models`)
-   **Controllers**: `BlogController`, `UserController`, `PageController` (located in `app/Http/Controllers`)
-   **Views**: Blade templates in `resources/views`, including a layout component in `resources/views/components/layout.blade.php`
-   **Migrations**: Located in `database/migrations`
-   **Seeders**: `DatabaseSeeder`, `CategorySeeder`, `PostsSeeder` in `database/seeders`

## Building and Running

### Using Docker (Recommended)

1.  **Environment Setup**:
    ```bash
    cp .env.example .env
    ```
2.  **Start Containers**:
    ```bash
    docker compose up -d
    ```
    The application will be available at `http://localhost:9001`.

### Local Development

1.  **Install Dependencies**:
    ```bash
    composer install
    npm install
    ```
2.  **Database Setup**:
    ```bash
    touch database/database.sqlite
    php artisan migrate --seed
    ```
3.  **Run Development Servers**:
    ```bash
    npm run dev
    # In a separate terminal
    php artisan serve
    ```

## Development Conventions

-   **Coding Style**: Follows standard Laravel conventions. [Laravel Pint](https://laravel.com/docs/11.x/pint) is used for linting.
-   **Styling**: Uses Tailwind CSS 4.0 with the Vite plugin (`@tailwindcss/vite`).
-   **Testing**: [PHPUnit](https://phpunit.de/) is the testing framework. Run tests with `php artisan test`.
-   **Routing**: Defined in `routes/web.php`. Features `auth` and `guest` middleware groups.
-   **Asset Management**: Managed by Vite. Build for production with `npm run build`.

## Directory Structure Highlights

-   `app/Http/Controllers`: Application logic.
-   `app/Models`: Eloquent models.
-   `database/migrations`: Database schema definitions.
-   `database/seeders`: Data seeding logic.
-   `resources/views`: Blade templates and UI components.
-   `public/`: Compiled assets and static files.
-   `compose.yaml`: Docker service orchestration.
-   `Dockerfile`: Multi-stage build for the PHP application.
-   `default.conf`: Nginx server configuration.
