# Database Design

# user 
| Attributes      | Description               | Data Type       | Constraints                                                 |
|-----------------|---------------------------|-----------------|-------------------------------------------------------------|
| user_id (PK)    | Unique identifier         | INTEGER         | PRIMARY KEY, auto-increment                                 |
| email           | Email address             | VARCHAR(320)    | UNIQUE, NOT NULL                                            |
| name            | User's name               | VARCHAR(255)    | NOT NULL                                                    |
| password        | User's password           | VARCHAR(255)    | NOT NULL                                                    |


# administrator
| Attributes      | Description                    | Data Type       | Constraints                                                 |
|-----------------|---------------------------     |-----------------|-------------------------------------------------------------|
| user_id (PK, FK)| Unique identifier, Foreign Key | INTEGER         | PRIMARY KEY, FOREIGN KEY REFERENCES user(user_id)           |
| job_title       | Job title of administrator     | VARCHAR(255)    |                                                             |


# clients
| Attributes      | Description                    | Data Type       | Constraints                                                 |
|-----------------|---------------------------     |-----------------|-------------------------------------------------------------|
| user_id (PK, FK)| Unique identifier, Foreign Key | INTEGER         | PRIMARY KEY, FOREIGN KEY REFERENCES user(user_id)           |
| address         | Client's address               | VARCHAR(255)    |                                                             |


# order
| Attributes       | Description                       | Data Type       | Constraints                              |
|----------------- |---------------------------        |-----------------|------------------------------------------|
| order_id (PK)    | Unique identifier                 | INTEGER         | PRIMARY KEY, auto-increment              |
| status           | Order status                      | VARCHAR(50)     | NOT NULL                                 |
| date             | Date of the order                 | DATE            | NOT NULL                                 |
| delivery_location| Delivery location                 | VARCHAR(255)    | NOT NULL                                 |
| total_price      | Total price of the order          | DECIMAL(10,2)   | NOT NULL                                 |
| user_id (FK)     | User ID associated with the order | INTEGER         | FOREIGN KEY REFERENCES client(user_id)   |


# product
| Attributes      | Description               | Data Type       | Constraints                                                 |
|-----------------|---------------------------|-----------------|-------------------------------------------------------------|
| product_id (PK) | Unique identifier         | INTEGER         | PRIMARY KEY, auto-increment                                 |
| name            | Name of the product       | VARCHAR(255)    | NOT NULL                                                    |
| calories        | Calories per serving      | INTEGER         |                                                             |
| stock_level     | Current stock level       | INTEGER         | NOT NULL                                                    |
| url             | URL of the product        | VARCHAR(255)    |                                                             |
| alt_text        | Alternative text for image| VARCHAR(255)    |                                                             |
| category        | Category of the product   | VARCHAR(50)     |                                                             |
| price           | Price of the product      | DECIMAL(10,2)   | NOT NULL                                                    |
| description     | Description of the product| TEXT            |                                                             |


# order_product
| Attributes         | Description                     | Data Type     | Constraints                                                 |
|-----------------   |---------------------------      |---------------|-------------------------------------------------------------|
| order_id (PK, FK)  | Order identifier, Foreign Key   | INTEGER       | PRIMARY KEY, FOREIGN KEY REFERENCES order(order_id)         |
| product_id (PK, FK)| Product identifier, Foreign Key | INTEGER       | PRIMARY KEY, FOREIGN KEY REFERENCES product(product_id)     |
| quantity           | Quantity of the product         | INTEGER       | NOT NULL                                                    |
| cup_size           | Cup size of the product         | VARCHAR(50)   |                                                             |
| milk_type          | Type of milk                    | VARCHAR(50)   |                                                             |


# review
| Attributes         | Description                           | Data Type       | Constraints                                   |
|-----------------   |---------------------------            |-----------------|-----------------------------------------------|
| review_id (PK)     | Unique identifier                     | INTEGER         | PRIMARY KEY, auto-increment                   |              
| rating             | Rating of the product                 | INTEGER         |                                               |
| date               | Date of the review                    | DATE            | NOT NULL                                      |
| text               | Review text                           | TEXT            | NOT NULL                                      |
| user_id (FK)       | User ID associated with the review    | INTEGER         | FOREIGN KEY REFERENCES client(user_id)        |
| product_id (FK)    | Product ID associated with the review | INTEGER         | FOREIGN KEY REFERENCES product(product_id)    |
| replies            | Replies to the review                 | TEXT            |                                               |
| replies_review_id  | Review ID for replies                 | INTEGER         | FOREIGN KEY REFERENCES review(review_id)      |


# TRIGGERS:


# 1. Update Stock Level Trigger:

This trigger automatically updates the stock level of products after an order is placed.
It should decrement the stock level of products included in the order.

# 2. Send Email Notification Trigger:

This trigger sends email notifications to clients after they place an order.
It should trigger upon order placement and send relevant information to the client's email address.

# 3. Update Product Availability Trigger:

This trigger updates the availability status of products based on their stock level.
It should mark products as unavailable when their stock level reaches zero.

# 4. Calculate Order Total Trigger:

This trigger calculates the total cost of an order based on the selected products and quantities.
It should update the order total field in the order table.

# 5. Update Order History Trigger:

This trigger updates the order history of clients after order placement.
It should maintain a record of all orders placed by each client for easy access and retrieval.