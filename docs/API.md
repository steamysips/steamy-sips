# API documentation

## These endpoints cover actions related to products, orders, users, and authentication. Public endpoints provide access to information without requiring authentication, while protected endpoints require users to be logged in to perform certain actions like creating, updating, or deleting resources.


|  Endpoint                                   |  Meaning                                                                    |
|---------------------------------------------|-----------------------------------------------------------------------------|
|            Public Endpoints                 |                                                                             | 
|---------------------------------------------|-----------------------------------------------------------------------------|
| GET /products                               | Get the list of all products available in the store.                        | 
| GET /products/?sort=<field>&order=<asc|desc>| Get the list of products sorted by a field in ascending or descending order.|
| GET /products/[id]                          | Get the details of a specific product by its ID.                            |
| GET /products/categories                    | Get the list of product categories.                                         | 
| GET /products/tags                          | Get the list of product tags.                                               |
|---------------------------------------------|-----------------------------------------------------------------------------|
|       Protected Endpoints (Require Login)   |                                                                             | 
|---------------------------------------------|-----------------------------------------------------------------------------|
| POST /products/create                       | Create a new product entry in the database.                                 |
| POST /products/[id]/delete                  | Delete a product with the specified ID.t                                    |
| POST /products/[id]/update                  | Update the details of a product with the specified ID.                      |
| POST /orders/create                         | Create a new order for products.                                            |
| GET /orders/[id]                            | Get the details of a specific order by its ID.                              |
| GET /orders/?user=[id]                      | Get the orders made by a specific user.                                     |
| GET /orders/?status=[status]                | Get the list of orders with a specific status.                              |
| POST /orders/[id]/update-status             | Update the status of an order with the specified ID.                        |
| GET /users                                  | Get the list of all users.                                                  |
| GET /users/[id]                             | Get the details of a specific user by their ID.                             |
| POST /users/[id]/delete                     | Delete a user with the specified ID.                                        |
| POST /users/[id]/update                     | Update the details of a user with the specified ID.                         |
| POST /logout                                | Log out the currently logged-in user.                                       |
