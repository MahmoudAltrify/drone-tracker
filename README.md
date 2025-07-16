<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Drone Tracker API

This is a Laravel-based real-time Drone Tracker system. It supports drone telemetry ingestion, danger classification (altitude, speed, and geofencing), and exposes a RESTful API for querying drones and their flight data.

## Features

- Realtime telemetry processing and danger classification
- Dangerous drone detection by altitude, speed, and no-fly zones
- API authentication using Laravel Sanctum
- Swagger (OpenAPI) documentation
- Dockerized environment for development and deployment
- Unit and feature tests included

---

## Prerequisites

Ensure you have the following installed:

- Docker & Docker Compose
- PHP >= 8.1 (only if running locally without Docker)
- Composer
- Node.js & NPM
- Laravel CLI (optional for local development)

---

## Getting Started

### Clone the repository

```bash
git clone https://github.com/MahmoudAltrify/drone-tracker.git
cd drone-tracker
```

---

### Environment Setup

Copy the example `.env` file:

```bash
cp .env.example .env
```
Composer install:

```bash
composer install
```
Generate application key:

```bash
php artisan key:generate
```

---

### Docker Setup

Build and run the app using Docker:

```bash
docker compose up --build -d
```

The API will be available at `http://localhost:8000`.

---

## Database & Seeders

Run migrations:

```bash
docker compose exec app php artisan migrate
```

Run seeders (e.g., to populate users and no-fly zones):

```bash
docker compose exec app php artisan db:seed
```

You can also run specific seeders:

```bash
docker compose exec app php artisan db:seed --class=NoFlyZoneSeeder
```

---

## Running Tests

To run all tests (unit and feature):

```bash
docker compose exec app php artisan test
```

To run only unit or feature tests:

```bash
docker compose exec app php artisan test --testsuite=Unit
docker compose exec app php artisan test --testsuite=Feature
```

---

## API Documentation

Swagger UI is available at:

```
http://localhost:8000/api/documentation
```

Or you can generate the docs using:

```bash
docker compose exec app php artisan l5-swagger:generate
```

---

## Authentication

The API uses token-based authentication via Laravel Sanctum.

1. **Login:**

```http
POST /api/v1/login
{
  "email": "admin@example.com",
  "password": "password"
}
```

The response will include a `token` that must be used in subsequent requests.

2. **Authenticated Requests:**

Include the token in the `Authorization` header:

```
Authorization: Bearer <your-token>
```

3. **Logout:**

```http
POST /api/v1/logout
```

---

## API Endpoints Overview

- `GET /v1/drones` – List all drones or filter by serial
- `GET /v1/drones/online` – Get all online drones
- `GET /v1/drones/nearby?lat=...&lng=...` – Get drones within 5KM
- `GET /v1/drones/{serial}/path` – Get drone flight path as GeoJSON
- `GET /v1/drones/dangerous` – List dangerous drones
- `POST /v1/login` – User login
- `GET /v1/me` – Get current authenticated user
- `POST /v1/logout` – Logout user

---

## Additional Notes

- All dangerous drone classifications (altitude, speed, no-fly zones) are modular and handled via the Strategy Pattern.
- Telemetry processing is centralized in a service class and is unit tested.
- The API uses JSON response format with success and error wrappers.

---

## License

This project is open-source and licensed under the MIT License.


