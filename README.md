# Task Management REST API

A robust REST API built with Laravel 11 for managing projects and tasks.

## Features
- **User Authentication**: Sanctum-based registration, login, and token management.
- **Project Management**: CRUD operations for projects with ownership enforcement.
- **Task Management**: CRUD operations for tasks within projects.
- **Filtering & Sorting**: Filter tasks by status/priority and sort by due date, priority, or creation time.
- **Quality Assurance**: 100% test pass rate with Pest, Level 5 PHPStan compliance.

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

## API Documentation

### Authentication
- `POST /api/register`: Register a new user.
- `POST /api/login`: Login and receive a Bearer token.
- `POST /api/logout`: Revoke current token (Protected).
- `GET /api/user`: Get authenticated user profile (Protected).

### Projects (Protected)
- `GET /api/projects`: List user's projects.
- `POST /api/projects`: Create a new project.
- `GET /api/projects/{id}`: View a project details.
- `PUT /api/projects/{id}`: Update a project.
- `DELETE /api/projects/{id}`: Delete a project.

### Tasks (Protected)
- `GET /api/projects/{project}/tasks`: List tasks in a project (Supports `status`, `priority`, `sort_by`, `sort_order`).
- `POST /api/projects/{project}/tasks`: Create a task in a project.
- `GET /api/tasks/{id}`: View task details.
- `PUT /api/tasks/{id}`: Update task.
- `DELETE /api/tasks/{id}`: Delete task.

## Development & testing
- Run tests: `php artisan test`
- Run Static Analysis: `./vendor/bin/phpstan analyse`
