## Foxes Rental Systems.

This software is suited for property managers who usually manages multiple properties for different landlords. It is a
web based application that allows the user to manage the properties, tenants, payments, and other related information.

## Features

- Manage properties
- Manage units
- Manage tenants
- Manage landlords
- Manage leases
- Manage payments
- Manage expenses
- Manage documents
- Manage accounting
- Manage users
- Manage roles
- Manage permissions
- Manage settings
- Manage reports
- Manage notifications
- Support ticket system
- Manage backups
- Activity logs

### -Tenant Features

- View leases
- View payments history
- View invoicing history
- Submit support tickets

### -Landlord Features

- View owned properties
- View owned units
- View associated expenses
- View relevant reports

## Installation
The system is based on Laravel 9.0. The installation process is the same as any other Laravel application.
- Clone the repository using ```git clone ```
- Navigate to the project directory using ```cd rental-management-system```
- Run ```composer install``` to install the dependencies
- Copy .env.example to .env using ```cp .env.example .env```
- Create a new blank database and update the .env file with the database credentials
- Run ```php artisan key:generate``` to generate the application key
- Run ```php artisan migrate``` to run the database migrations
- Run ```php artisan db:seed``` to seed the database and create the default user
- Run ```php artisan storage:link``` to create a symbolic link to the storage folder
- Run ```php artisan optimize:clear``` to clear all the cached files ready for testing/production
- Run ```php artisan serve``` to start the development server
- Navigate to ```http://localhost:8000``` to access the application

The default user credentials are:
- Email: admin@admin.com
- Password: demo123#

### Pre-requisites
- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer

-When landlords and tenants are created,an email is sent to them to set their password. Therefore, you need to configure the mail settings in the .env file.

-The application uses some automated tasks to perform some tasks. You need to configure the cron job to run the following commands:
- ```* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1```.
The automated tasks are:
- Send lease expiry notifications
- Send payment reminders
- Generate rent invoices
- Perform periodic backups

### Logos
Due to different appearance settings you may see different logos in the application. The logo is stored in the following locations:
- ```public/assets/images/logo.png```
- ```public/assets/images/logo2x.png```
- ```public/assets/images/logo-dark.png```
- ```public/assets/images/logo-dark2x.png```
- ```public/assets/images/logo-dark-small.png```
- ```public/assets/images/logo-dark-small2x.png```
- ```public/assets/images/logo-small.png```
- ```public/assets/images/logo-small2x.png```

Update the logo in these locations to change the logo in the entire application.



