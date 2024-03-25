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

Download the project:

```bash
git clone git@github.com:creme332/steamy-sips.git
```

Move to the root directory of the project:

```bash
cd steamy-sips
```

Install composer dependencies:

```bash
composer update
```

In the [`src/core/`](../src/core/config.php) folder, create a `.env` file with the following contents:

```php
PUBLIC_ROOT="http://localhost/steamy-sips/public"

DB_HOST="localhost"
DB_USERNAME="root"
DB_PASSWORD=""

PROD_DB_NAME="cafe"
TEST_DB_NAME="cafe_test"

BUSINESS_GMAIL=""
BUSINESS_GMAIL_PASSWORD=""
```

Some important notes:

- Update the values assigned to `DB_USERNAME` and `DB_PASSWORD` with your MySQL login details.
- If your Apache server is serving from a port other than the default one, add the new port number to `PUBLIC_ROOT` (
  eg., `http://localhost:443/steamy-sips/public`) .
- `BUSINESS_GMAIL` and `BUSINESS_GMAIL_PASSWORD` are the credentials of the Gmail account from which emails will be sent
  whenever a client places an order. It is recommended to use
  a [Gmail App password](https://knowledge.workspace.google.com/kb/how-to-create-app-passwords-000009237)
  for `BUSINESS_GMAIL_PASSWORD` instead of your actual gmail account password.

## Setup production database

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

The path to the SQL dump might must be modified if you are not in the root directory of the project.

## Setup testing database

If you want to run tests for the application, you must setup a database for testing. To do so, repeat the same
instructions as the setup for the production database except name the testing database `cafe_test`.

## Setup linting and formatting

This step is optional if you do not plan on editing the JS and CSS files. Node.js is required to install the linter and
formatter for JS and CSS files. For more details on the linters and formatters used, see
our [coding standards](CODING_STANDARDS.md).

In the root directory of the project, run:

```bash
npm install
```
