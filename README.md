# Task Management REST API

A robust REST API built with Laravel 11 for managing projects and tasks.

## Features
- **User Authentication**: Sanctum-based registration, login, and token management.
- **Project Management**: CRUD operations for projects with ownership enforcement.
- **Task Management**: CRUD operations for tasks within projects.
- **Filtering & Sorting**: Filter tasks by status/priority and sort by due date, priority, or creation time.
- **Quality Assurance**: 100% test pass rate with Pest, Level 5 PHPStan compliance.

### 🌟 Bonus Features
- **Docker Setup**: Laravel Sail integration for easy containerized development.
- **Redis Caching**: Project and Task listings are cached with automatic invalidation.
- **Queued Actions**: Event-driven architecture with background jobs for non-blocking operations.
- **Service/Repository Pattern**: Decoupled architecture for better testability and scalability.
- **API Rate Limiting**: Protected endpoints (60 requests/min).
- **Pagination**: Support for paginated results in all listing endpoints.
- **Soft Deletes**: Safety first approach to data deletion.
- **CI/CD Pipeline**: GitHub Actions workflow for automated testing and analysis.
- **API Documentation**: Interactive documentation generated via Scribe.

## Installation

1. Clone the repository and navigate to the project directory.
2. Install dependencies:
   ```bash
   composer install
   ```
3. Setup environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Configure your database in `.env`.
5. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

### Running with Docker (Sail)
```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```

## API Documentation
The API documentation is available at `/docs` (when running locally) or can be found in `public/docs/index.html`.

## Development & testing
- Run tests: `php artisan test`
- Run Static Analysis: `./vendor/bin/phpstan analyse`
- Generate Docs: `php artisan scribe:generate`
