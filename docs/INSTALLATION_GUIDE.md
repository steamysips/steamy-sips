# Installation

The following setup instructions are tailored for Linux. If you are using Windows, the steps are generally similar, but
some commands may differ. Please adapt accordingly.

## Prerequisites

- PHP (v8.1 preferred)
- Apache2
- MySQL (v15.1 preferred)
- Composer with its executable on your $PATH
- Git

## Setup project

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

## Setup database

Connect to your MySQL server:

```bash
mysql -u username -p
```

Create a database `cafe`:

```bash
create database cafe;
```

Import data to the database from SQL dump:

```bash
mysql -u username -p cafe < resources/cafe.sql
```

Insert your database information in [`src/core/config.php`](../src/core/config.php):

```php
// define database credentials for localhost
define('DBNAME', 'cafe'); // name of database
define('DBUSER', 'username'); // name of database user
define('DBPASS', 'password'); // password of database user
```

## Setup linting and formatting

This step is optional if you do not plan on editing the JS and CSS files. Node.js is required to install the linter and
formatter for JS and CSS files. For more details on the linters and formatters used, see
our [coding standards](CODING_STANDARDS.md).

In the root directory of the project, run:

```bash
npm install
```
