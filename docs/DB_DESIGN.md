# Database Design

## person

| Attribute | Description       | Data type | Constraint                    |
| --------- | ----------------- | --------- | ----------------------------- |
| id        | Unique identifier | INTEGER   | Primary key,  auto-increment. |

## administrator

| Attribute | Description           | Data type    | Constraint                                                                             |
| --------- | --------------------- | ------------ | -------------------------------------------------------------------------------------- |
| id        | Unique identifier     | INTEGER      | Primary key. Foreign key referencing `id` in `person` table.                           |
| email     | Email address of user | VARCHAR(320) | Unique and case-sensitive. Must contain an @ symbol and be at least 3 characters long. |

## client
