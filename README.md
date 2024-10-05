# Event Booker - Backend Using Laravel 11

This project allows you to create and manage events booking.

## Prerequisites

-   PHP 8.2 or higher
-   MySQL database
-   Composer for dependency management

## Installation

1. Install Composer Packages

```bash
composer install
```

2. Create .env file by coping .env.example

3. Generate App key Using

```bash
php artisan key:generate
```

4. Setup Database Connections and Mails Ports in .env file

```bash
# Set this in .env must for sending mail
QUEUE_CONNECTION=database
```

5. Migrate Database

```bash
php artisan migrate --seed
```

6. Now Run

```bash
php artisan serve

php artisan queue:listen
```
