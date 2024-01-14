# Installation

The following setup instructions are tailored for Linux. If you are using Windows, the steps are generally similar, but
some commands may differ. Please adapt accordingly.

## Prerequisites

- PHP 8.1
- phpMyAdmin 5.2  (optional)
- Apache2
- Composer installed globally
- Git

> **Note**: The project has been tested only on PHP 8.1, but it might work for other versions as well.

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

Create a MySQL database named `cafe` and import the SQL dump at `resources/db.sql`.

Insert your database information in [`src/core/config.php`](../src/core/config.php):

```php
// define database credentials for localhost
define('DBNAME', ''); // name of database
define('DBUSER', ''); // name of database user
define('DBPASS', ''); // password of database user
```

## Setup linting and formatting

This step is optional if you do not plan on editing the JS and CSS files. Node.js is required to install the linter and
formatter for JS and CSS files. For more details on the linters and formatters used, see
our [coding standards](CODING_STANDARDS.md).

In the root directory of the project, run:

```bash
npm install
```
