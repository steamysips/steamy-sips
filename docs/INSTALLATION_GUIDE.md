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
git clone git@github.com:steamy-sips/steamy-sips.git
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
DB_NAME="cafe"

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

If you want to run tests, create `.env.testing` file in the root directory:

```
DB_HOST="localhost"
DB_USERNAME="root"
DB_PASSWORD=""
DB_NAME="cafe_test"
API_BASE_URI="http://steamy.localhost/api/v1/"
```

## Database setup

Start your MySQL server:

```bash
sudo service mysql start
```

Create production database `cafe`:

```bash
mysql -u root -p < resources/database/cafe_schema.sql
```

Import data to production database:

```bash
mysql -u root -p cafe < resources/database/cafe_data.sql
```

If you want to run tests with composer, you must first set up a separate database `cafe_test` for testing:

```bash
mysql -u root -p < resources/database/cafe_test_schema.sql
mysql -u root -p cafe_test < resources/database/cafe_test_data.sql
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

## Modifying CSS/JS Files

If you need to make changes to the CSS or JavaScript files located in the `public` folder, you need to run the following
command in your terminal:

```bash
npm run build
```

This command will compile your changes and update the necessary files for your application.