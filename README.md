# steamy-sips ☕

A CRUD application for a coffee shop built using LAMP stack.

## Features

- User authentication
- Users can leave reviews
- Admin dashboard
- MVC pattern
- Uses PDO
- Styled minimally with PicoCSS
- Mobile-responsive
- Good core web vital scores

## Installation

The following setup instructions are tailored for Linux. If you are using Windows, the steps are generally similar, but
some commands may differ. Please adapt accordingly.

### Prerequisites:

- PHP 8.1 installed globally
- phpMyAdmin 5.2  (optional)
- Apache2
- Composer installed globally
- Git

### Setup project

Navigate to the document root of your server:

```bash
cd /var/www/html # your root might be different
```

Download project:

```bash
git clone git@github.com:creme332/steamy-sips.git
```

Move to the root directory of the project:

```bash
cd steamy-sips
```

Install dependencies:

```bash
composer update
```

### Setup database

Create a MySQL database and import data.

Insert your database information in [`src/core/config.php`](src/core/config.php):

```php
// define database credentials for localhost
define('DBNAME', ''); // name of database
define('DBUSER', ''); // name of database user
define('DBPASS', ''); // password of database user
```

## Usage

Start Apache server, MySQL database, and display live error log:

```bash
sudo service apache2 restart && sudo service mysql start && sudo tail -f /var/log/apache2/error.log
```

Open http://localhost/steamy-sips/public/ in your browser to see the website.

## To-do

- [ ] write phpUnit tests for model
- [ ] About Us heading missing from website
- [ ] how to use package imports, autoreload
- [ ] add animation to make wave disappear
- [ ] read about [namespaces](https://phptherightway.com/#namespaces)
- [ ] user must log in to write review
- [ ] fix accessibility issues
- [x] how does php session work
- [ ] follow phptherightway method
    - [ ] pdo
    - [x] unicode set
- [x] test website when database is not connected
- [ ] Try to import/export mysqldump
- [ ] Deploy on render using Docker

## References

- https://github.com/php-pds/skeleton
- https://github.com/kevinisaac/php-mvc