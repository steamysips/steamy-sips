# Installation

The following setup instructions are tailored for Linux. If you are using Windows, the steps are generally similar, but
some commands may differ. Please adapt accordingly.

## Prerequisites

- PHP (v8.1 preferred)
- Apache2
- MySQL (v15.1 preferred)
- Composer with its executable on your $PATH
- Git
- NPM (optional)

## Project setup

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

## Environment setup

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

Update the values assigned to `DB_USERNAME` and `DB_PASSWORD` with your MySQL login details.

> [!IMPORTANT]  
> If your Apache server is serving from a port other than port 80, include the port number in `PUBLIC_ROOT` (
> e.g., `http://localhost:443/steamy-sips/public`) .

## Mail setup

1. Choose an email address (gmail account) from which you want emails to be sent.
2. Go to https://myaccount.google.com/ and login using your desired email.
3. Search for `2-Step Verification` in the `Security` section and enable it.
4. Search for `App passwords` in the security section and create an app password with a name of your choice.
5. In the `src/core/.env` file created earlier, fill `BUSINESS_GMAIL`
   and `BUSINESS_GMAIL_PASSWORD`. `BUSINESS_GMAIL_PASSWORD` should be set to your app password.

> [!IMPORTANT]  
> Remove any spaces from your app password when entering it in the `.env` file.

> [!IMPORTANT]  
> Ensure that you have no firewall or antivirus software (e.g. Avast, McAfee) that block emails. If the mailing service
> is not working
> set `$this->mail->SMTPDebug = SMTP::SERVER;` in the `Mailer.php` file and inspect the error.

## Database setup

Start your MySQL server and connect to its monitor:

```bash
sudo service mysql start
mysql -u <username> -p
```

> **Note**: `<username>` is a placeholder for your MySQL username.

Create a database `cafe`:

```sql
create database cafe;
use cafe;
source resources/database/dump/cafe.sql;
exit;
```

The path to the SQL dump might must be modified if you are not in the root directory of the project.

If you want to run unit tests with composer, you must first set up a separate database for testing. To do so, repeat the
same
instructions as above except name the testing database `cafe_test`:

```sql
create database cafe_test;
use cafe_test;
source resources/database/dump/cafe.sql;
exit;
```

## PHP setup

Ensure that the [`variables_order`](https://www.php.net/manual/en/ini.core.php#ini.variables-) directive in
your `php.ini`
file is set to `"EGPCS"`. Without this, the application will
not be able to load environment variables properly in `src/core/config.php` and you will get an array key error.
You can use `php --ini` to find the location of your `php.ini` file.

## Linting and formatting setup

This step is optional if you do not plan on editing the JS and CSS files. NPM is required to install the linter and
formatter for JS and CSS files. For more details on the linters and formatters used, see
our [coding standards](CODING_STANDARDS.md).

In the root directory of the project, run:

```bash
npm install
```

## Autoload setup

Whenever changes are made to the autoload settings in `composer.json`, you must run `composer dump-autoload`.