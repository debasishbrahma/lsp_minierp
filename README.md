# Mini ERP Application

A Laravel-based Mini ERP system for managing products and quotations, featuring role-based access control, soft deletes, Redis caching and Tailwind CSS for styling. This project uses Pest for testing and supports MySQL, Redis, and Mailtrap.io for email notifications.

## Prerequisites

-   **PHP**: 8.1 or higher
-   **Composer**: 2.0 or higher
-   **Node.js and npm**: 18.x or higher (for Tailwind CSS)
-   **MySQL**: 8.0 or higher
-   **Redis**: 7.0 or higher
-   **Git**: For version control
-   **Visual Studio Code**: Recommended for development
-   **Mailtrap.io**: For email testing (Quotation status update at [mailtrap.io](https://mailtrap.io/))
-   **Operating Systems**: Windows (PowerShell) or Linux (Bash)

## Setup Instructions

### 1. Clone the Repository

Clone the project to your local machine:

```bash
git clone https://github.com/debasishbrahma/lsp_minierp.git mini-erp
cd mini-erp
```

### 2. Laravel Setup

Install PHP dependencies and configure the application.

#### Install Dependencies

```bash
composer update
```

#### Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` with the following settings:

```
APP_NAME="Mini ERP"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_erp
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=<your-mailtrap-username>
MAIL_PASSWORD=<your-mailtrap-password>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@mini-erp.com"
MAIL_FROM_NAME="Mini ERP"
```

#### Generate Application Key

```bash
php artisan key:generate
```

#### Run the DB command

```bash
php artisan db:create
```

#### Install Node.js Dependencies

```bash
npm install
```

#### Compile Assets

Compile Tailwind CSS and other frontend assets:

```bash
npm run build
```

### 3. Manual Database and Services Setup

Configure MySQL, Redis.

#### MySQL Database

1. Install MySQL:

    - **Windows**: Download and install MySQL Community Server from [mysql.com](https://dev.mysql.com/downloads/). Use MySQL Installer to set up.

    - **Linux**:

        ```bash
        sudo apt update
        sudo apt install mysql-server
        sudo systemctl start mysql
        sudo systemctl enable mysql
        ```

2. Create the database:

    ```bash
    mysql -u root -p
    ```

    In the MySQL prompt:

    ```sql
    CREATE DATABASE mini_erp;
    GRANT ALL PRIVILEGES ON mini_erp.* TO 'root'@'localhost' WITH GRANT OPTION;
    FLUSH PRIVILEGES;
    EXIT;
    ```

    Update `.env` with your MySQL credentials if different (e.g., `DB_USERNAME`, `DB_PASSWORD`).

3. Run migrations:

    ```bash
    php artisan migrate
    ```

4. (Optional) Seed the database:

    ```bash
    php artisan db:seed
    ```

#### Redis Setup

1. Install Redis:

    - **Windows**:

        - Download Redis from [github.com/microsoftarchive/redis](https://github.com/microsoftarchive/redis/releases) or use WSL.
        - Extract and run `redis-server.exe`.

    - **Linux**:

        ```bash
        sudo apt update
        sudo apt install redis-server
        sudo systemctl start redis
        sudo systemctl enable redis
        ```

2. Install PHP Redis extension:

    - **Windows**:

        - Download `php_redis.dll` for PHP 8.1 from [pecl.php.net](https://pecl.php.net/package/redis).

        - Place in `C:\php\ext` (adjust for your PHP installation).

        - Add to `php.ini`:

            ```
            extension=redis
            ```

    - **Linux**:

        ```bash
        sudo apt install php8.1-redis
        ```

3. Verify Redis:

    ```bash
    redis-cli ping
    ```

    Should return `PONG`.

### 4. Run the Application

Open three terminal windows (or tabs) to run the necessary services.

#### Terminal 1: Serve the Application

```bash
php artisan serve
```

Access at `http://localhost:8000`. Default credentials from database/seeders/Databaseseeder file(if seeded):

-   Admin: `admin@example.com` / `password`
-   Sales: `sales@example.com` / `password`

#### Terminal 2: Run Queue Worker

Process queued jobs (e.g., `QuotationStatusUpdated` notifications) with Redis:

```bash
php artisan queue:work redis
```

#### Terminal 3: Watch Assets

Compile and watch Tailwind CSS changes:

```bash
npm run dev
```

### 5. Run App & Tests

Run migrations for the test environment (MySQL):

```bash
php artisan migrate:fresh && php artisan db:seed
```

Run all Pest tests and check in log file:

```bash
php artisan test
```
