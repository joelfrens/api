[![Build Status](https://travis-ci.com/joelfrens/api.svg?branch=master)](https://travis-ci.com/joelfrens/api)

## Setup instructions

This is a Laravel 7 installation and I used MAMP with PHP 7.4

### Clone the repository 

``` git clone git@github.com:joelfrens/api.git ```

### Create env files

The project needs a .env and .env.testing file. I have added a .env.example and .env.testing.example files. Please change the database settings to match your local setup

### Run composer

``` composer install ```

### Run migrations

Create two databases on your mysql setup. Laravel uses a separate database to run the test. ex. demo and demo_test
Make changes in you .env file and .env.testing file to change the db name, username and password

``` php artisan migrate ```

### Run the seeder

This will add some data to the products, product_translations, categories and languages table s

``` php artisan db:seed ```

### Run the integration test

``` php artisan test --env=testing --testdox ```

### Testing Client

I used Postman to test the API. The API expects the language code to be sent using HTTP headers X-localization en-gb or fr-ch.
- X-localization: en-gb or fr-ch

By default the language will be en-gb if you don't specify any headers


Please also set the following headers when making any request using Postman

- Content-Type: application/json
- Accept: application/json

