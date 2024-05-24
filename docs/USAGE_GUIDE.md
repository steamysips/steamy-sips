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

Visit [`http://steamy.localhost/`](http://steamy.localhost/) in your browser to view the client website.

> [!CAUTION]
> The website should not be accessed from `localhost/steamy-sips/public` because the project uses root-relative URLs.
>
> Root-relative URLs are URLs that start with a forward slash (/) and are relative to the root directory of the website.
> When a website is accessed from different base URLs, such as localhost/steamy-sips/public, the root-relative URLs may
> not resolve correctly, leading to broken links, missing resources, or other issues.

## Run tests

> [!IMPORTANT]  
> You must start your MySQL database to be able to run tests in the `tests/models` folder.
> To run tests in the `tests/api` folder, you need to start both your Apache server and your MySQL database.

To run all tests:

```bash
composer test
```

To run tests only in `tests/models`:

```bash
composer modeltest
```

To run tests only in `tests/api`:

```bash
composer apitest
```

## Export database

To export only the schema of the `cafe` database:

```bash
mysqldump -u root -p --no-data --databases cafe > resources/database/cafe_schema.sql
```

To export only the data in the `cafe` database:

```bash
mysqldump -u root -p --no-create-info cafe > resources/database/cafe_data.sql
```