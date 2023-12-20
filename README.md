
# Superban

This is a request limit wrapper for laravel. It works like the default laravel throttle on steroids

## Installation

Add the package respository to your composer file by running the code below
```bash
composer config repositories.superban '{"type": "vcs", "url": "https://github.com/Joemires/superban-edenlife-test"}' --file composer.json
```

Install the package on your project using composer
```bash
composer require joemires/superban:dev-master
```

### Setup
The package is 100% ready to be used without any configuration, but if you want to change it's configuration

First publish the package config to your config directory by running

```bash
php artisan vendor:publish --provider="Joemires\Superban\SuperbanServiceProvider" --tag="config"
```

Then you can edit the `superban.php` file in your config folder

Or you can add `SUPERBAN_CACHE_DRIVER` to your .env and update to what you want

```env
SUPERBAN_CACHE_DRIVER=redis
SUPERBAN_IDENTIFIER=ip
```
## Usage
It's very simple to use, just add superban to your request middleware and that's all

```php
Route::get('/superban-protected')->middleware('superban')
```
The example above allows `10 requests` from a user in `1 minute` to the `superban-protected` route, and bans the user for `1 minute` if he or she makes more than the allowed request.
The user is banned either by id if authenticated or ip address

You can change `SUPERBAN_IDENTIFIER` to fingerprint if you want a more precised tracking for unauthenticated users

You can customize the allowed request, request period and the banned period anyhow you want

```php
Route::get('/superban-protected')->middleware('superban:10,1,1');
// Allows 10 request from a user in a minute and bans the user for 1 minutes if he exhausts the request

// OR

Route::get('/superban-protected')->middleware('superban:10,1,5');
// Allows 10 request from a user in a minute and bans the user for 5 minutes if he exhausts the request
```

You can also add the middleware to group of routes

```php
Route::middleware('superban')->group(function () {
    Route::get('/superban-protected');
});
```
