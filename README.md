# Car Empire Management System

A comprehensive Laravel-based web application for managing car dealership operations.

## Features

- **User Authentication**: Secure login system with role-based access
- **Admin Dashboard**: Bootstrap-powered responsive dashboard
- **User Management**: Admin and regular user roles
- **Modern UI**: Clean and professional interface with Bootstrap 5

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL/MariaDB
- XAMPP (recommended for local development)

## Installation

### 1. Database Setup

1. Open phpMyAdmin
2. Create a new database named `cems_db`
3. Create a user with the following credentials:
   - Username: `john_dev`
   - Password: `YeEN0)*1VlA879BG`
   - Grant all privileges to the `cems_db` database

### 2. Application Setup

1. Clone or download this project to your XAMPP htdocs directory
2. Open Command Prompt in the project directory
3. Run the setup script:
   ```bash
   setup.bat
   ```

   Or manually run these commands:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   ```

### 3. Start the Application

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Login Credentials

### Admin User
- **Email**: admin@carempire.com
- **Password**: admin123
- **Role**: Admin (full access)

### Regular User
- **Email**: john@carempire.com
- **Password**: john123
- **Role**: User (limited access)

## Project Structure

```
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/LoginController.php
│   │   └── DashboardController.php
│   ├── Models/
│   │   └── User.php
│   └── Providers/
├── config/
│   ├── app.php
│   └── database.php
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   └── index.php
├── resources/
│   └── views/
│       ├── auth/
│       ├── dashboard/
│       └── layouts/
└── routes/
    └── web.php
```

## Features Overview

### Dashboard
- Welcome message with user information
- Statistics cards showing key metrics
- Recent sales table
- Quick action buttons
- System status indicators

### Navigation
- Responsive sidebar navigation
- Role-based menu items
- User dropdown with profile and logout options

### Authentication
- Secure login form with validation
- Remember me functionality
- Automatic redirect to dashboard after login
- Logout functionality

## Technology Stack

- **Backend**: Laravel 10
- **Frontend**: Bootstrap 5, Font Awesome
- **Database**: MySQL
- **Authentication**: Laravel's built-in authentication
- **Styling**: Custom CSS with Bootstrap components

## Development

To start development:

1. Make sure XAMPP is running
2. Ensure the database is set up correctly
3. Run `php artisan serve` to start the development server
4. Access the application at `http://localhost:8000`

## Support

For any issues or questions, please check the Laravel documentation or contact the development team.

## License

This project is licensed under the MIT License.





















