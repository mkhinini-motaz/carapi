# carapi: the First Car Rental API

carapi is the back end of First Car Rental, a system I built for a small car rental agency in Tunisia. It is a Laravel API that a phone app calls. I wrote it by hand between July 2024 and June 2025 (26 commits), before I used coding agents. The phone app is in [car](https://github.com/mkhinini-motaz/car). I later rebuilt the whole system with Claude Code as [car-rental](https://github.com/mkhinini-motaz/car-rental), which is public.

## What it does

The API has six routes (`routes/api.php`), all under the `api` prefix.

- `POST /login`: takes a phone number and a password and returns the user with a JWT access token.
- Behind the `auth` middleware:
  - `GET /me`: the logged-in user.
  - `POST /logout`.
  - `GET /car`: a paginated list of cars. It takes `page`, `perPage`, `search`, `orderBy`, `orderDirection`, `starts_at` and `ends_at`. Search matches manufacturer, model and plate. With a period, it keeps the cars that may be free then (`app/Repositories/CarRepository.php`).
  - `GET /car/{carId}`: one car.
  - `GET /contract`: the contracts of the user's agency, paginated, with the same kind of search, ordering and period filters (`app/Repositories/ContractRepository.php`).

The data model is in the migrations (`database/migrations`):

- **Agency**: a name; users, cars and clients belong to an agency.
- **Car**: daily price, licence plate, serial number, manufacturer, model, year, fiscal horsepower, cylinders, transmission, fuel type, first circulation date, and the end dates of the circulation tax, the insurance and the technical control.
- **Client**: name, phone, birth date, gender, ID number and driver licence number and date.
- **Contract**: a client, a car, a start and end time and a daily price.

Other details:

- Dates are stored as Unix timestamps (`app/Support/DoesntConvertDates.php`), and models use soft deletes.
- Responses go through resource classes (`app/Http/Resources`).
- Controllers for agencies and clients, and store and update requests for every model, exist, but their routes are not registered.
- Seeders and factories create agencies, cars, clients and contracts.

## Stack

- PHP 8.2, Laravel 11
- `tymon/jwt-auth` 2.1
- SQLite by default in `.env.example`
- PHPUnit 11, Laravel Pint 1

## Layout

```
app/Http/Controllers/   auth, car, contract, agency, client
app/Http/Requests/      store and update requests
app/Http/Resources/     JSON resources
app/Models/             Agency, Car, Client, Contract, User
app/Repositories/       paginated, searchable queries for cars and contracts
app/Support/            helpers (search, date periods)
database/               migrations, factories, seeders
routes/api.php          the API routes
```

## Running it

```
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
php artisan test
```

The code is archived and not maintained, and it may need an older PHP to run.

## Status

Archived. I rebuilt First Car Rental with Claude Code as [car-rental](https://github.com/mkhinini-motaz/car-rental).
