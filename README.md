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
- Perfect web vital scores

## Installation

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

Create a MySQL database:

Edit config file at `src/core/config.php` with your details.

## Usage

Start server and MySQL database and display live error log:

```bash
sudo service apache2 restart && sudo service mysql start && sudo tail -f /var/log/apache2/error.log
```

Open http://localhost/steamy-sips/public/ in your browser to see the website.

## To-do

- [x] add landing page
    - [x] add svg wave
    - [x] add animations
- [ ] write phpUnit tests for model
- [x] make landing page responsive
- [ ] how to use package imports, autoreload
- [ ] add animation to make wave disappear
- [ ] read about [namespaces](https://phptherightway.com/#namespaces)
- [ ] user must log in to write review
- [ ] how does php session work
- [ ] follow phptherightway method
    - [ ] pdo
    - [ ] unicode set
- [ ] test website when database is not connected
- [ ] Use mysqldump for database backups
- [ ] Deploy on render using Docker

## References
- https://github.com/php-pds/skeleton
- https://github.com/kevinisaac/php-mvc