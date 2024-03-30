# API documentation

These endpoints cover actions related to products, orders, users, and authentication. Public endpoints provide access to information without requiring authentication, while protected endpoints require users to be logged in to perform certain actions like creating, updating, or deleting resources.


## Public

|  Endpoint                                    |  Meaning                                                                    |
|----------------------------------------------|-----------------------------------------------------------------------------|
| GET /products                                | Get the list of all products available in the store.                        | 
| GET /products/?sort=<field>&order=<asc, desc>| Get the list of products sorted by a field in ascending or descending order.|
| GET /products/[id]                           | Get the details of a specific product by its ID.                            |
| GET /products/categories                     | Get the list of product categories.                                         | 
| GET /products/tags                           | Get the list of product tags.                                               |
| GET /products/{id}/reviews                   | Get all reviews for a particular product by its ID.                         |
| GET /products/?group-by=category             | Get the list of products grouped by category.                               |
| GET /orders/?group-by=status                 | Get the list of orders grouped by status.                                   |
| GET /users                                   | Get the list of all users.                                                  |
| GET /users/{id}                              | Get the details of a specific user by their ID.                             |
| GET /clients                                 | Get the list of all clients.                                                |
| GET /clients/{id}                            | Get the details of a specific client by their ID.                           |
| GET /administrators                          | Get the list of all administrators.                                         |
| GET /administrators/{id}                     | Get the details of a specific administrator by their ID.                    |
 

## Protected


The following endpoints require user to be logged in:

|  Endpoint                                   |  Meaning                                                                    | 
|---------------------------------------------|-----------------------------------------------------------------------------|
| POST /products/create                       | Create a new product entry in the database.                                 |
| POST /products/[id]/delete                  | Delete a product with the specified ID.t                                    |
| POST /products/[id]/update                  | Update the details of a product with the specified ID.                      |
| POST /orders/create                         | Create a new order for products.                                            |
| GET /orders/[id]                            | Get the details of a specific order by its ID.                              |
| GET /orders/?user=[id]                      | Get the orders made by a specific user.                                     |
| GET /orders/?status=[status]                | Get the list of orders with a specific status.                              |
| POST /orders/[id]/update-status             | Update the status of an order with the specified ID.                        |
| POST /orders/[id]/update                    | Update the details of an order with the specified ID.                       |
| POST /orders/{id}/delete                    | Delete an order with the specified ID.                                      |
| GET /users                                  | Get the list of all users.                                                  |
| POST /users/create                          | Create a new user entry in the database.                                    |
| GET /users/[id]                             | Get the details of a specific user by their ID.                             |
| POST /users/[id]/delete                     | Delete a user with the specified ID.                                        |
| POST /users/[id]/update                     | Update the details of a user with the specified ID.                         |
| POST /clients/create                        | Create a new client entry in the database.                                  |
| POST /clients/{id}/update                   | Update the details of a client with the specified ID.                       |
| POST /clients/{id}/delete                   | Delete a client with the specified ID.                                      |
| POST /administrators/create                 | Create a new administrator entry in the database.                           |
| POST /administrators/{id}/update            | Update the details of an administrator with the specified ID.               |
| POST /administrators/{id}/delete            | Delete an administrator with the specified ID.                              |
| GET /reviews/{id}/product                   | Get all reviews for a particular product by its ID.                         |
| POST /reviews/create                        | Create a new review for a product.                                          |
| POST /reviews/{id}/update                   | Update the details of a review with the specified ID.                       |
| POST /reviews/{id}/delete                   | Delete a review with the specified ID.                                      |
| GET /districts                              | Get the list of all districts.                                              |
| GET /districts/{id}                         | Get the details of a specific district by its ID.                           |
| POST /districts/create                      | Create a new district entry in the database.                                |
| POST /districts/{id}/update                 | Update the details of a district with the specified ID.                     |
| POST /districts/{id}/delete                 | Delete a district with the specified ID.                                |
| POST /logout                                | Log out the currently logged-in user.                                       |


