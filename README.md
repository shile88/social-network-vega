<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Social network app

> ### Laravel API app build as a learning project. 

---------
# Getting started

## Installation

Clone the repository

    https://github.com/shile88/MilosIvanis_Omega_SocialNetwork.git

Switch to the repo folder

    cd MilosIvanis_Omega_SocialNetwork

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Run the database migrations and seeders (**<span style="color: green;">Set the database connection in .env before migrating using SQLite</span>**)

    php artisan migrate --seed

To make these files accessible from the web, you should create a symbolic link from public/storage to storage/app/public.

    php artisan storage:link

Start the local development server

    php artisan serve

App uses queue so open different terminal and type this so, it runs

    php artisan queue:work

You can now access the server at http://localhost:8000

**TL;DR command list**

    git clone https://github.com/shile88/MilosIvanis_Omega_SocialNetwork.git
    cd MilosIvanis_Omega_SocialNetwork
    composer install
    cp .env.example .env
    php artisan key:generate

## Database seeding

**Populate the database with seed data with relationships which includes admin, users, posts, comments, likes, replies, reports, connections. <span style="color: red;">This can help you to quickly start testing the api, but it's fake data so could be some overlying id's but nothing to make problems.</span>**

***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

    php artisan migrate:fresh --seed

# Basic flow and app testing

## Programs needed

- Used: PHP Storm for coding, Postman for route testing, DB Browser for database checking

<span style="color: green;">Postman collection with environment is provided in GitHub repository, so you can test all routes with included authorization and token in all routes.</span>
Open up Postman and check all the provided request. All logic is separated in folders.
App has many features like:
- `Registration and login with access restrictions based on that`
- `Sending and receiving friend connections`
- `CRUD operations with posts, comments, likes, reports...`
- `Admin and user privileges`
- `Permission to see, comment and like based on friendship status`
- `Feed with your friends posts`
- `Password reset`
- `Email notification for new comments, likes and password reset`
- `User and posts reporting`
- `Not permitting access to user or posts based on report status`
- `Multi level comment`
- `Export in csv for all user data`
- `app/Model`


----------

# Code overview

## Dependencies

- No external dependencies are added

## Folders

- `app` - Contains all the Eloquent models
- `app/exception` - Contains add exceptions
- `app/Http/Controllers/Api` - Contains all the api controllers
- `app/Http/Middleware` - Contains the admin and banned posts middleware
- `app/Http/Requests` - Contains all the api requests
- `app/Http/Resources` - Contains all the api resources
- `app/Model` - Contains all the models
- `app/Notifications` - Contains the notification for new likes, comments and password reset
- `app/Policies` - Contains the policies for post, comment and like
- `app/Services` - Contains the service for appropriate controller
- `database/factories` - Contains the data for all tables
- `database/migration` - Contains all the tables for app
- `database/seeders` - Contains the info about seeded the database
- `config` - Contains all the application configuration files
- `resources/views/mail` - Contains the blade files for notification emails
- `routes/api` - Contains all the api routes
- `database/seeds` - Contains the database seeder
- `routes` - Contains all the api routes defined in api.php file
- `tests/feature` - Contains some tests I tried

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

----------





