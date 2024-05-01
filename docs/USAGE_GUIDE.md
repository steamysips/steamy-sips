# Usage

You must have completed the [installation instructions](INSTALLATION_GUIDE.md) before
proceeding.

## Run project

Start your Apache server and your MySQL database:

```bash
sudo service apache2 restart && sudo service mysql start
```

Optionally, you can display a live error log:

```bash
sudo tail -f /var/log/apache2/error.log
```

Visit `http://steamy.localhost/` in your browser to view the client website.

## Run tests

Assuming that your MySQL database is running, in the root directory of the project run tests:

```bash
composer test
```

## Backup database

To export database with `mysqldump`:

```bash
mysqldump -u root -p cafe > resources/database/dump/cafe.sql
```