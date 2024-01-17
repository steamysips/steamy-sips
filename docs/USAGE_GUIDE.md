# Usage

You must first install the project as per the [installation instructions](INSTALLATION_GUIDE.md) before
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

Go to http://localhost/steamy-sips/public/ in your browser to see the website.

## Testing

To run tests:

```bash
./vendor/bin/phpunit tests
```