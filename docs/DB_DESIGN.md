# Database Design

- [Database Design](#database-design)
  - [Schema diagram](#schema-diagram)
  - [Tables](#tables)
    - [user](#user)
    - [administrator](#administrator)
    - [client](#client)
    - [order](#order)
    - [order\_product](#order_product)
    - [review](#review)
    - [product](#product)
    - [district](#district)
    - [comment](#comment)
    - [password\_change_\request](#password_change_request)
    - [store](#store)
    - [store\_product](#store_product)


## Schema diagram

![Database schema](../resources/diagrams/db-schema.png)

## Tables

### user

| Attribute   | Description             | Data Type         | Constraints                                       |
| ----------- | ----------------------- | ----------------- | ------------------------------------------------- |
| user_id     | ID of User              | INT(11) UNSIGNED  | PRIMARY KEY, auto-increment                       |
| email       | Email address           | VARCHAR(320)      | Not Null, Unique, Check (email format)            |
| first_name  | User's first name       | VARCHAR(255)      | Nullable, Check (first_name length > 2)           |
| last_name   | User's last name        | VARCHAR(255)      | Nullable, Check (last_name length > 2)            |
| password    | Hashed password         | VARCHAR(255)      | Nullable, Check (password length > 8)             |
| phone_no    | User's phone number     | VARCHAR(255)      | Not Null, Check (phone_no length > 6)             |

### administrator

| Attribute      | Description                                       | Data Type        | Constraints                                |
|----------------| ------------------------------------------------- | ---------------- | ------------------------------------------ |
| user_id        | Unique identifier for the administrator           | INT(11) UNSIGNED | Primary Key, Foreign Key (user_id -> user) |
| job_title      | Job title of administrator                        | VARCHAR(255)     | Not Null, Check (job_title length > 3)     |
| is_super_admin | Whether the administrator is a super admin or not | TINYINT(1)       | DEFAULT 0                                  |

### client

| Attribute   | Description             | Data Type         | Constraints                                |
| ----------- | ----------------------- | ----------------- | ------------------------------------------ |
| user_id     | ID of client            | INT(11) UNSIGNED  | Primary Key, Foreign Key (user_id -> user) |
| street      | Client's street address | VARCHAR(255)      | Not Null, Check (street length > 3)        |
| city        | Client's city           | VARCHAR(255)      | Not Null, Check (city length > 2)          |
| district_id | Client's district       | INT(11) UNSIGNED  | Foreign Key (district_id -> district)      |

### order

| Attribute    | Description                                    | Data Type         | Constraints                                           |
| ------------ | ---------------------------------------------- | ----------------- | ----------------------------------------------------- |
| order_id     | ID of order                                    | INT(11) UNSIGNED  | PRIMARY KEY, auto-increment                           |
| status       | Status of the order (e.g., pending, completed) | VARCHAR(20)       | Default 'pending'                                     |
| created_date | Date the order was created                     | DATETIME          | Default (current_timestamp())                         |
| pickup_date  | Date of the order pickup                       | DATETIME          | Nullable, Check (pickup_date >= created_date)         |
| client_id    | Identifier of the client placing the order     | INT(11) UNSIGNED  | Foreign Key (client_id -> client)                     |
| store_id     | Identifier of the store fulfilling the order   | INT(10) UNSIGNED  | Foreign Key (store_id -> store)                       |

### order_product

| Attribute  | Description                  | Data Type     | Constraints                                                 |
| ---------- | ---------------------------- | ------------- | ----------------------------------------------------------- |
| order_id   | ID of order                  | INT(11)       | Foreign Key (order_id -> order)                             |
| product_id | ID of product                | INT(11)       | Foreign Key (product_id -> product)                         |
| quantity   | Quantity of the product      | INT(11)       | Nullable, Check (quantity > 0)                              |
| cup_size   | Cup size of the product      | VARCHAR(20)   | Nullable, Must be one of: `small`, `medium`, `large`        |
| milk_type  | Type of milk                 | VARCHAR(20)   | Nullable, Must be one of: `almond`, `coconut`, `oat`, `soy` |
| unit_price  | Unit price of the product   | DECIMAL(10,2) | Nullable                                                    |

### review

| Attribute        | Description                                  | Data Type         | Constraints                                 |
| ---------------- | -------------------------------------------- | ----------------- | ------------------------------------------- |
| review_id        | ID of review                                 | INT(11) UNSIGNED  | PRIMARY KEY, auto-increment                 |
| rating           | Rating of the product                        | INT(11) UNSIGNED  | Not Null, Must be between 1 and 5 inclusive |
| created_date     | Date and time when the review was created    | DATETIME          | Not Null, Default (current_timestamp())     |
| text             | Review text                                  | VARCHAR(2000)     | Not Null, Check (text length >= 2)          |
| client_id        | ID of client                                 | INT(11) UNSIGNED  | Foreign Key (client_id -> client)           |
| product_id       | ID of product                                | INT(11) UNSIGNED  | Foreign Key (product_id -> product)         |


### product

| Attribute    | Description                                 | Data Type         | Constraints                                         |
| ------------ | ------------------------------------------- | ----------------- | --------------------------------------------------- |
| product_id   | ID of product                               | INT(11) UNSIGNED  | PRIMARY KEY, auto-increment                         |
| name         | Name of the product                         | VARCHAR(255)      | Not Null, Check (name length > 2)                   |
| calories     | Calories per serving                        | INT(11) UNSIGNED  | Nullable, Check (calories >= 0)                     |
| img_url      | URL of the product                          | VARCHAR(255)      | Not Null, Must end with `.png`, `.jpeg`, or `.avif` |
| img_alt_text | Alternative text for image                  | VARCHAR(150)      | Not Null, Check (length between 5 and 150)          |
| category     | Category of the product                     | VARCHAR(50)       | Not Null, Check (category length > 2)               |
| price        | Price of the product                        | DECIMAL(10,2)     | Not Null                                            |
| description  | Description of the product                  | TEXT              | Nullable, Check (description length > 0)            |
| created_date  | Date and time when  added to the database  | DATETIME          | Not Null, Default (current_timestamp())             |

### district

| Attribute   | Description   | Data Type         | Constraints                                                |
| ----------- | ------------- | ----------------- | ---------------------------------------------------------- |
| district_id | District ID   | INT(11) UNSIGNED  | PRIMARY KEY, auto-increment                                |
| name        | District name | VARCHAR(30)       | Not Null, Unique, Must be one of: `Moka`, `Port Louis`,    |
|             |               |                   | `Flacq`, `Curepipe`, `Black River`, `Savanne`,             |
|             |               |                   | `Grand Port`, `Riviere du Rempart`, `Pamplemousses`,       |
|             |               |                   | `Mahebourg`, `Plaines Wilhems`                             |

### comment

| Attribute          | Description                                 | Data Type          | Constraints                                     |
| ------------------ | ------------------------------------------- | ------------------ | ----------------------------------------------- |
| comment_id         | ID of comment                               | INT(10) UNSIGNED   | PRIMARY KEY, auto-increment                     |
| text               | Text content of the comment                 | VARCHAR(2000)      | Not Null, Must be between 1 and 5 inclusive     |
| created_date       | Date and time when the comment was created  | DATETIME           | Not Null, Default (current_timestamp())         |
| parent_comment_id  | ID of the parent comment                    | INT(10) UNSIGNED   | Not Null, Check (text length >= 2)              |
| user_id            | ID of user who posted the comment           | INT(10) UNSIGNED   | Foreign Key (client_id -> client)               |
| review_id          | ID of review associated with the comment    | INT(10) UNSIGNED   | Foreign Key (product_id -> product)             |

### password_change_request

| Attribute   | Description                                        | Data Type          | Constraints                       |
| ----------- | -------------------------------------------------- | ------------------ | --------------------------------- |
| request_id  | ID of request                                      | INT(11) UNSIGNED   | PRIMARY KEY, auto-increment       |
| user_id     | ID of user requesting the password change          | INT(11) UNSIGNED   | Foreign Key (user_id -> user)     |
| token_hash  | Hashed token for the password change request       | VARCHAR(255)       | Not Null                          |
| expiry_date | Date and time when the request token expires       | DATETIME           | Not Null                          |
| used        | Flag indicating if the request token has been used | TINYINT(1)         | Not Null, Default 0               | 

### store

| Attribute    | Description                                   | Data Type          | Constraints                            |
| ------------ | --------------------------------------------- | ------------------ | -------------------------------------- |
| store_id     | ID of store                                   | INT(10) UNSIGNED   | PRIMARY KEY, auto-increment            |
| phone_no     | Phone number of the store                     | VARCHAR(255)       | Not Null                               |
| street       | Street address of the store                   | VARCHAR(255)       | Not Null                               |
| coordinate   | Geographic coordinates of the store location  | POINT              | Not Null                               |
| district_id  | ID of district where the store is located     | INT(10) UNSIGNED   | Foreign Key (district_id -> district)  |
| city         | City where the store is located               | VARCHAR(255)       | Not Null                               |

### store_product

| Attribute    | Description                                      | Data Type          | Constraints                            |
| ------------ | ------------------------------------------------ | ------------------ | -------------------------------------- |
| store_id     | ID of store                                      | INT(11) UNSIGNED   | Foreign Key (store_id -> store)        |
| product_id   | ID of product                                    | INT(11) UNSIGNED   | Foreign Key (product_id -> product)    |
| stock_level  | Current stock level of the product in the store  | INT(10) UNSIGNED   | Not Null, Default 0                    |
