## Quick Starts

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
php artisan db:seed
```

6. Start development servers

```bash
php artisan serve

url look like http://localhost:8000
then in url type like below and hit enter
http://localhost:8000/app
```

### Export and Import

1. Laravel 11 and higher

```bash
php artisan make:queue-batches-table
php artisan make:notifications-table
```

2. All apps

```bash
php artisan vendor:publish --tag=filament-actions-migrations
php artisan migrate
```

3.a. Make Export file

```bash
php artisan make:filament-exporter ModelName --generate
Like
php artisan make:filament-exporter State --generate
```

3.b. Make Import file

```bash
php artisan make:filament-importer ModelName --generate
Like
php artisan make:filament-importer State --generate
```

4. Add to Resoucefile(StateResource.php) under actions in the $table

```bash
use App\Filament\Exports\StateExporter;
use Filament\Tables\Actions\ExportAction;

use App\Filament\Imports\StateImporter;
use Filament\Tables\Actions\ImportAction;

ExportAction::make()
    ->exporter(StateExporter::class),

ImportAction::make()
    ->importer(StateExporter::class)
```

5. Add to AdminPanelProvider under middleware

```bash
->databaseNotifications()
```

6. Start queue

```bash
php artisan queue:listen
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

6. Create user

```bash
php artisan make:filament-user

give credentials and sign in
```

7. Generate Filament Resourse through Commant

```bash
# Generate Simple view
php artisan make:filament-resource ModelName

# Generate view with table and form
php artisan make:filament-resource ModelName --generate

# Generate view with table, form and view
php artisan make:filament-resource ModelName --generate --view

Like

php artisan make:filament-resource Country
php artisan make:filament-resource State --generate
php artisan make:filament-resource City --generate --view

```

### For more guideline please read the below link

```bash
1. https://filamentphp.com/docs/3.x/panels/installation

2. https://youtube.com/playlist?list=PL6tf8fRbavl3jfL67gVOE9rF0jG5bNTMi&si=4rM8ZNU5APuxfBz5

3. https://github.com/filamentphp/filament

4. https://laracasts.com/series?topics%5B0%5D=filament
```

### install shield for spatie role and permission

#1. Install Package

```bash
composer require bezhansalleh/filament-shield
```

#2. Configure Auth Provider
#2.1. Publish the config and setup your auth provider model.

```bash
php artisan vendor:publish --tag="filament-shield-config"
```

#2.2 Add the HasRoles trait to your auth provider model:

```bash
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

#3. Setup Shield

```bash
3.1 Without Tenancy:
php artisan shield:setup
```

3.2 With Tenancy:

```bash
php artisan shield:setup --tenant=App\\Models\\Team
# Replace Team with your tenant model
```

#4. Install for Panel

```bash
4.1 Without Tenancy
php artisan shield:install admin
# Replace 'admin' with your panel
Or instead of the above command you can register the plugin manually in your xPanelProvider:
->plugins([
    \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
])
```

4.2 With Tenancy:

```bash
php artisan shield:install admin --tenant --generate-relationships
# Replace 'admin' with your panel ID

Or instead of the above command you can register the plugin and enable tenancy manually in your xPanelProvider:
->tenant(YourTenantModel::class)
->tenantMiddleware([
    \BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant::class,
], isPersistent: true)
->plugins([
    \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
])
```

#Usage

#Users (Assigning Roles to Users)

1. Without Tenancy

```bash

// Using Select Component
Forms\Components\Select::make('roles')
    ->relationship('roles', 'name')
    ->multiple()
    ->preload()
    ->searchable(),

// Using CheckboxList Component
Forms\Components\CheckboxList::make('roles')
    ->relationship('roles', 'name')
    ->searchable(),
```

2. With Tenancy

```bash
// Using Select Component
Forms\Components\Select::make('roles')
      ->relationship('roles', 'name')
      ->saveRelationshipsUsing(function (Model $record, $state) {
           $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
      })
     ->multiple()
     ->preload()
     ->searchable(),

// Using CheckboxList Component
Forms\Components\CheckboxList::make('roles')
      ->relationship(name: 'roles', titleAttribute: 'name')
      ->saveRelationshipsUsing(function (Model $record, $state) {
           $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
      })
     ->searchable(),
```

#Core Commands

# Setup Shield

```bash
php artisan shield:setup [--tenant=]
e.g
php artisan shield:setup --tenant=Team

# Install Shield for a panel

php artisan shield:install {panel} [--tenant]
e.g
php artisan shield:install admin --tenant

# Generate permissions/policies

php artisan shield:generate --all

# Create super admin

php artisan shield:super-admin [--user=] [--panel=] [--tenant=]
e.g
php artisan shield:super-admin --user=1 --panel=admin --tenant=1

# Publish Role Resource

php artisan shield:publish {panel}
e.g
php artisan shield:publish admin

```

### Or Read the below link in details

```bash
https://filamentphp.com/plugins/bezhansalleh-shield
```
