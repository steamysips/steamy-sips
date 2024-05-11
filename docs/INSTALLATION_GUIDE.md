# Installation

The following setup instructions are tailored for Linux. If you are using Windows, the steps are generally similar, but
some commands may differ. Please adapt accordingly.

## Prerequisites

- PHP (v8.1 preferred)
- Apache2
- MySQL (v15.1 preferred)
- Composer with its executable on your $PATH
- Git
- NPM (v10 preferred)

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
composer install
```

Install dependencies for frontend:

```bash
npm install
```

Build the frontend:

```bash
npm run build
```

> [!IMPORTANT]  
> You must run all the `composer` and `npm` commands inside the root directory (`\var\www\html\steamy-sips`) of the
> project.

In the root directory of the project, create a `.env` file with the following contents:

```php
DB_HOST="localhost"
DB_USERNAME="root"
DB_PASSWORD=""

PROD_DB_NAME="cafe"
TEST_DB_NAME="cafe_test"

BUSINESS_GMAIL=""
BUSINESS_GMAIL_PASSWORD=""
```

Update the values assigned to `DB_USERNAME` and `DB_PASSWORD` with your MySQL login details.

`BUSINESS_GMAIL` and `BUSINESS_GMAIL_PASSWORD` are the credentials of the Gmail account from which emails will be sent
whenever a client places an order.

> [!NOTE]  
> It is recommended to use
> a [Gmail App password](https://knowledge.workspace.google.com/kb/how-to-create-app-passwords-000009237)
> for `BUSINESS_GMAIL_PASSWORD` instead of your actual gmail account password.

## Database setup

Start your MySQL server and connect to its monitor:

```bash
sudo service mysql start
mysql -u <username> -p
```

> [!NOTE]  
> `<username>` is a placeholder for your MySQL username. If your username is `root`, the command to run
> becomes `mysql -u root -p`

Create a database `cafe`:

```sql
create database cafe;
use cafe;
source resources/database/dump/cafe.sql;
exit;
```

The path to the SQL dump must be modified your present working directory is not the root directory of the project.

If you want to run tests with composer, you must first set up a separate database for testing. To do so, repeat the
same instructions as above except name the testing database `cafe_test`:

```sql
create database cafe_test;
use cafe_test;
source resources/database/dump/cafe.sql;
exit;
```

## Virtual host setup

Create a new file `steamy.conf` inside the `/etc/apache2/sites-available` directory:

```
<VirtualHost *:80>
    ServerName steamy.localhost

    DocumentRoot /var/www/html/steamy-sips/public

    <Directory /var/www/html/steamy-sips/public>
      Options Indexes MultiViews FollowSymLinks SymLinksIfOwnerMatch
      AllowOverride All
      Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

Add the following lines to your `/etc/hosts` file:

```
127.0.0.1 steamy.localhost
::1 steamy.localhost
```

> [!TIP]  
> If you are on Windows or WSL2, your host file is located at `C:\Windows\System32\drivers\etc` and must be edited with
> administrator privileges.

Enable your site:

```
sudo a2ensite steamy
```

Restart your apache server:

```
sudo service apache2 restart
```

## PHP setup

Ensure that the [`variables_order`](https://www.php.net/manual/en/ini.core.php#ini.variables-) directive in
your `php.ini`
file for apache is set to `"EGPCS"`. Without this, the application will
not be able to load environment variables properly in `src/core/config.php` and you will get an array key error.
You can use `php --ini` to find the location of your `php.ini` file.

## Autoload setup

Whenever changes are made to the autoload settings in `composer.json`, you must run `composer dump-autoload`.