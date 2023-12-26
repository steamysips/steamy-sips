# LAMP project

A template for CRUD applications using LAMP stack.

## Features
- MVC pattern
- Styled with PicoCSS library (3KB)
- Mobile-responsive
- Perfect web vital scores

## Installation
### Prerequisites:
- PHP 8.1
- phpMyAdmin 5.2 
- Apache 2
```bash
git clone ssh:
```

### Database
Create database on mysql by running the following script:

Edit config file at `src/core/config.php` with your details.
## Usage
Start server and database and display log:
```bash
sudo service apache2 restart && sudo service mysql start && sudo tail -f /var/log/apache2/error.log
```

Open `http://localhost/skeleton/public/` in your browser to see the website.

## To-do
- [ ] Edit domain name by changing server name: https://github.com/Bashar-Ahmed/ShopEase
- [ ] add landing page
  - [ ] add svg wave
  - [ ] add animations
  - [ ] use intersection observer API from [fireship video](https://youtu.be/T33NN_pPeNI?si=qmortFFiXdDzlF0e)
- [ ] write phpUnit tests for model
- [ ] user must login to write review
- [ ] follow phptherightway method
  - [ ] pdo
  - [ ] unicode set
- [ ] test website when database is not connected
- [ ] renaming variables with f2 not working. install php extension
- [ ] Decide what to do with compliance validators...
- [ ] Use mysqldump for database backups
- [ ] Deploy on render using Docker

## References
- https://github.com/kevinisaac/php-mvc