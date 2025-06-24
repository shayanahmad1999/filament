## Quick Start

### Installation

1. Clone the repository
```bash
git clone https://github.com/shayanahmad1999/filament.git
cd filament
```

2. Create Database Manually to avoid composer error
```bash
Use Current Terminal
mysql -u root -p
CREATE DATABASE IF NOT EXISTS my_new_database;
EXIT;
```

2. Install dependencies
```bash
composer install
```

3. Set up environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations
```bash
php artisan migrate
```

6. Start development servers
```bash
php artisan serve
```

### OR install filament step by step guide

1. Install Laravel
```bash
laravel new
then enter
project_name
and select not-starter-kit
```

2. Configure your database in `.env`
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_passwo
```

3. Run migrations
```bash
php artisan migrate
```

4. Install Filament
```bash
composer require filament/filament:"^3.3" -W

php artisan filament:install --panels
```

5. Start development servers
```bash
php artisan serve

url look like http://localhost:8000
then in url type like below and hit enter
http://localhost:8000/admin
```