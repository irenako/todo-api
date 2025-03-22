# Todo API

This is a RESTful API for managing users, their tasks and statistics.

## Project Overview

This API provides endpoints to manage users and their tasks, featuring:

-   User management (CRUD operations)
-   Task management (CRUD operations + deleting all tasks with status "new")
-   Task statistics per user and per app
-   Pagination and sorting capabilities

## Tech Stask

- Framework: Laravel 12
- Database: MariaDB
- Language: PHP 8.1+
- Composer


## Installation

1. Install dependencies:

```bash
composer install
```

2. Copy the environment file:

```bash
cp .env.example .env
```

4. Configure your MySQL connection in `.env`:

```
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=todo_api
DB_USERNAME=root
DB_PASSWORD=
```

5. Generate application key:

```bash
php artisan key:generate
```

6. Run migrations:

```bash
php artisan migrate
```

7. Start the development server:

```bash
php artisan serve
```

## API Endpoint 

API can tested by using provided Postman collection. (Todo API.postman_collection.json)

## Testing

Run the test suite:

```bash
php artisan test
```

## License

This project is open-sourced software licensed under the MIT license.
