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

Start your MySQL server and connect to its monitor:

```bash
sudo service mysql start
mysql -u <username> -p
```

> **Note**: `<username>` is a placeholder for your MySQL username.

Create a database `cafe`:

```bash
create database cafe;
```

Select the database:

```
use cafe;
```

Import data to the database from the SQL dump:

```bash
source resources/database/dump/cafe.sql
```

In the [`src/core/`](../src/core/config.php) folder, create a `.env` file with the following contents:

```php
APP_ENV="dev"
DEV_ROOT="http://localhost/steamy-sips/public"
DB_HOST="localhost"
DB_NAME="cafe"
DB_USERNAME="root"
DB_PASSWORD=""
```

Update the values assigned to `DB_USERNAME` and `DB_PASSWORD` with your MySQL login details.
If your Apache server is serving from a port other than the default one, update `DEV_ROOT`.

## Setup linting and formatting

This step is optional if you do not plan on editing the JS and CSS files. Node.js is required to install the linter and
formatter for JS and CSS files. For more details on the linters and formatters used, see
our [coding standards](CODING_STANDARDS.md).

In the root directory of the project, run:

```bash
npm install
```
