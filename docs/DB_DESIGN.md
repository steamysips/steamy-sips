# Database Design

# user 
| Attributes      | Description               | Data Type       | Constraints                                                 |
|-----------------|---------------------------|-----------------|-------------------------------------------------------------|
| user_id         | Unique identifier         | INTEGER         | PRIMARY KEY, auto-increment                                 |
| email           | Email address             | VARCHAR(320)    | UNIQUE, NOT NULL, Must match the pattern %@%.%              |
| name            | User's name               | VARCHAR(255)    | NOT NULL                                                    |
| password        | User's password           | VARCHAR(255)    | NOT NULL, Must be greater than 8 characters                 |
| phone_no        | User's phone number       | INTEGER         | Must be greater than 6 characters                           |


# administrator
| Attributes      | Description                                       | Data Type        | Constraints                                       |
|-----------------|-------------------------------------------        |----------------- |---------------------------------------------------|
| user_id         | Unique identifier, Foreign Key                    | INTEGER          | PRIMARY KEY, FOREIGN KEY REFERENCES user(user_id) |
| job_title       | Job title of administrator                        | VARCHAR(255)     |                                                   |
| is_superadmin   | Whether the administrator is a super admin or not | BOOLEAN          | DEFAULT false                                     |



# clients
| Attributes      | Description                    | Data Type       | Constraints                                                 |
|-----------------|---------------------------     |-----------------|-------------------------------------------------------------|
| user_id         | Unique identifier, Foreign Key | INTEGER         | PRIMARY KEY, FOREIGN KEY REFERENCES user(user_id)           |
| street          | Client's street address        | VARCHAR(255)    |                                                             |
| city            | Client's city                  | VARCHAR(255)    |                                                             |
| district        | Client's district              | VARCHAR(255)    |                                                             |


# order
| Attributes       | Description                       | Data Type       | Constraints                              |
|----------------- |---------------------------        |-----------------|------------------------------------------|
| order_id         | Unique identifier                 | INTEGER         | PRIMARY KEY, auto-increment              |
| status           | Order status                      | VARCHAR(50)     | NOT NULL                                 |
| created_date     | Date the order was created        | DATE            |                                          |
| pickup_date      | Date of the order pickup          | DATE            |                                          |
| street           | Delivery street address           | VARCHAR(255)    |                                          |
| city             | Delivery city                     | VARCHAR(255)    |                                          |
| district         | Delivery district                 | VARCHAR(255)    |                                          |
| total_price      | Total price of the order          | DECIMAL(10,2)   | NOT NULL                                 |
| user_id          | User ID associated with the order | INTEGER         | FOREIGN KEY REFERENCES client(user_id)   |


# product
| Attributes      | Description               | Data Type       | Constraints                                                 |
|-----------------|---------------------------|-----------------|-------------------------------------------------------------|
| product_id      | Unique identifier         | INTEGER         | PRIMARY KEY, auto-increment                                 |
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
| order_id           | Order identifier, Foreign Key   | INTEGER       | PRIMARY KEY, FOREIGN KEY REFERENCES order(order_id)         |
| product_id         | Product identifier, Foreign Key | INTEGER       | PRIMARY KEY, FOREIGN KEY REFERENCES product(product_id)     |
| quantity           | Quantity of the product         | INTEGER       | NOT NULL                                                    |
| cup_size           | Cup size of the product         | VARCHAR(50)   | Must be one of: 'small', 'medium', 'large'                  |
| milk_type          | Type of milk                    | VARCHAR(50)   |                                                             |


# review
| Attributes         | Description                           | Data Type       | Constraints                                   |
|-----------------   |---------------------------            |-----------------|-----------------------------------------------|
| review_id          | Unique identifier                     | INTEGER         | PRIMARY KEY, auto-increment                   |              
| rating             | Rating of the product                 | INTEGER         | Must be between 1 and 5                       |
| date               | Date of the review                    | DATE            | NOT NULL                                      |
| text               | Review text                           | TEXT            | NOT NULL                                      |
| user_id            | User ID associated with the review    | INTEGER         | FOREIGN KEY REFERENCES client(user_id)        |
| product_id         | Product ID associated with the review | INTEGER         | FOREIGN KEY REFERENCES product(product_id)    |
| replies            | Replies to the review                 | TEXT            |                                               |
| replies_review_id  | Review ID for replies                 | INTEGER         | FOREIGN KEY REFERENCES review(review_id)      |



# Stored Procedures:

1. Add User Account Procedure:

This procedure adds a new user account to the system.
Parameters: email, name, password, phone_no.
Functionality: Inserts a new record into the user table with the provided details.
Constraints: Ensure that the email is unique, password is encrypted before insertion.

2. Add Administrator Account Procedure:

This procedure adds a new administrator account to the system.
Parameters: email, name, password, phone_no, job_title, is_superadmin.
Functionality: Inserts a new record into the administrator table with the provided details.
Constraints: Ensure that the email is unique, password is encrypted before insertion. Only users with is_superadmin privilege can execute this procedure.

3. Place Order Procedure:

This procedure enables clients to place orders.
Parameters: status, pickup_date, created_date, street, city, district, total_price, user_id.
Functionality: Inserts a new order record into the order table with the provided details.
Constraints: Ensure the user ID exists and the total price is non-negative.


# Triggers:

1. Update Stock Level Trigger:

This trigger automatically updates the stock level of a product after an order is placed.
Trigger Type: AFTER INSERT ON order_product table.
Functionality: Decreases the stock level of products included in the order based on the quantity ordered.
Constraints: Ensure the stock level does not go below zero.

2. Admin Account Addition Restriction Trigger:

This trigger prevents unauthorized users from adding new administrator accounts.
Trigger Type: BEFORE INSERT ON administrator table.
Functionality: Checks if the user attempting to add the administrator account has superadmin privileges. If not, the insertion is aborted.
Constraints: Ensure only users with superadmin privileges can add new administrator accounts.

3. Send Email Notification Trigger:

This trigger sends an email notification to the client when an order is placed.
Trigger Type: AFTER INSERT ON order table.
Functionality: Retrieves the client's email address associated with the order and sends an email notification confirming the order placement.
Constraints: Ensure the email is successfully sent without errors.

4. Update Order History Trigger:

This trigger updates the order history when an order is placed.
Trigger Type: AFTER INSERT ON order table.
Functionality: Inserts a new record into the order_history table, capturing details of the placed order such as order ID, status, pickup date, created date, street, city, district, total price, and user ID.
Constraints: Ensure the order history is accurately updated with the latest order information.