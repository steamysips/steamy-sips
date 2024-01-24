# steamy-sips ☕

A CRUD application for a coffee shop built using LAMP stack.

## Features

- User authentication
- Nested comment system on product page
- Admin dashboard
- MVC pattern
- Uses PDO for accessing database
- Styled minimally with PicoCSS
- Tested with PHPUnit?
- Documented with phpDoc
- Mobile-responsive

## Documentation

All documentation (installation instructions, usage guide, ...) is available in the [`docs`](docs) folder.

## To-do
- [ ] create `cafe` database with `user` table.
- [ ] shop page:
  - [ ] make search bar functional
  - [ ] add filter options
- [ ] shopping cart
  - [ ] make remove button functional 
  - [ ] make checkout button functional
  - [ ] reposition close button
- [ ] product page
  - [ ] make add to cart button functional
  - [ ] add date to comment
  - [ ] fix size of image
  - [ ] make reply button functional
- [ ] profile page
  - [ ] make edit profile button functional
  - [ ] make delete account button functional
  - [ ] make orders summary dynamic (use php variables)
- [ ] header for admins should be different (use a different template for Admin??)
- [ ] update phpDoc in views for variables defined controllers
- [ ] create admin dashboard
- [ ] write phpUnit tests for model. how to setup database
- [ ] add system requirements pdf
- [ ] create a REST API
- [ ] update registration form
- [ ] install json schema validator: justinrainbow/json-schema or opis/json-schema

## License

This project is an adaptation of the [`php-pds/skeleton`](https://github.com/php-pds/skeleton) filesystem, which is
licensed under the Creative Commons
Attribution-ShareAlike
4.0 International License. Please see the [LICENSE](LICENSE) file for details on the original license.

## References

- https://github.com/kevinisaac/php-mvc