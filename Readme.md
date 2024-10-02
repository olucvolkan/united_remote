# PHP RESTful API Project

This project is a RESTful API built using PHP. The project follows a multi-layer architecture, leveraging the repository pattern, dynamic DTO validation, and a modular routing system to create a robust and scalable API.

## Project Structure

The project is organized into multiple directories to separate concerns and improve maintainability. Below is a description of each directory and its purpose:

## File Structure

```
public/
├── index.php                # Entry point for the application

src/
├── Controller/              # API controllers for managing business logic
│   ├── AccountController.php
│   └── CustomerController.php
│
├── Core/                    # Core classes and system components
│   ├── BaseController/      # Base controller to handle shared functionality
│   ├── Database/            # Database connection and management classes
│   ├── Factory/             # Factory classes for dynamic loading
│   ├── Http/                # Request and Response handling
│   ├── Repository/          # Base repository classes and interfaces
│   ├── Router/              # Routing classes for defining routes
│   └── Validator/           # Validation classes for dynamic data validation
│
├── DTO/                     # Data Transfer Objects (DTO) for validating input data
│   ├── AccountDepositDTO.php
│   ├── AccountTransferDTO.php
│   ├── AccountWithdrawDTO.php
│   └── CustomerDTO.php
│
├── Repositories/            # Repository classes for database interactions
│   └── CustomerRepository.php
│
├── Routes/                  # Route definitions and loading files
│   └── web.php
│
├── Service/                 # Service layer for business logic and operations
│
└── Utils/                   # Utility classes for environment and status code management
├── Env.php
└── HttpStatusCode.php
```

## Requirements

- PHP 8.0 or higher
- Composer
- MySQL or MariaDB
- Docker (optional for containerized setup)

## Installation

1. **Clone the repository:**

    ```bash
    git clone https://github.com/olucvolkan/united_remote.git
    ```

2. **Navigate to the project directory:**

    ```bash
    cd united_remote
    ```

3. **Install dependencies using Composer:**

    ```bash
    composer install
    ```

4. **Configure your environment variables:**

   Create a `.env` file in the root directory and add your database credentials and other configurations:

    ```
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=customer_api
    DB_USERNAME=root
    DB_PASSWORD=yourpassword
    ```

   5. **Set up the database:**

      Use the following SQL command to create the necessary tables and schema:

 ```sql
CREATE DATABASE customer_api;
USE customer_api;

CREATE TABLE customers (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
surname VARCHAR(100) NOT NULL,
balance FLOAT NOT NULL DEFAULT 0
);

CREATE TABLE transactions (
id INT AUTO_INCREMENT PRIMARY KEY,
customer_id INT NULL,
type ENUM ('deposit', 'withdraw', 'transfer') NOT NULL,
amount DECIMAL(10, 2) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL,
CONSTRAINT transactions_ibfk_1 FOREIGN KEY (customer_id) REFERENCES customers (id)
);

CREATE INDEX customer_id ON transactions (customer_id);
```

6. **Start the local development server:**

    ```bash
     docker-compose up -d --build 
    ```

7. **Test the API using Postman or cURL:**

## Usage

### Customer Endpoints

- `GET /api/customers/{id}` - Retrieves details of a specific customer by ID.
- `POST /api/customers` - Creates a new customer.
- `PUT /api/customers/{id}` - Updates an existing customer by ID.
- `DELETE /api/customers/{id}` - Deletes a customer by ID.

### Account Endpoints

- `GET /api/accounts/{id}` - Retrieves account balance for a specific customer by ID.
- `POST /api/accounts/{id}/deposit` - Deposits a specified amount into the customer's account.
- `POST /api/accounts/{id}/withdraw` - Withdraws a specified amount from the customer's account.
- `POST /api/accounts/transfer` - Transfers funds from one customer account to another.

### Example Request and Response

To create a new customer, send a `POST` request to `/api/customers` with the following payload:

**Request Body:**

```json
{
  "name": "John",
  "surname": "Doe",
  "balance": 100.50
}
```
**Response:**
```json
{
  "message": "Customer added",
  "id": 10
}
```


To update a customer, send a `PUT` request to `/api/customers/10` with the following payload:

**Request Body:**

```json
{
  "name": "Updated",
  "surname": "Doe",
  "balance": 100.50
}
```
**Response:**
```json
{
  "message": "Customer updated"
}
```


To get a customer, send a `GET` request to `/api/customers/10` with the following payload:

**Response:**
```json
{
  "id": 10,
  "name": "John",
  "surname": "Doe",
  "balance": "100.52"
}
```


To delete a customer, send a `DELETE` request to `/api/customers/10` with the following payload:

**Response:**
```json
{
  "message": "Customer deleted"
}
```

To give a deposit to customer, send a `POST` request to `/api/accounts/{accountId}/deposit` with the following payload:

**Request Body:**

```json
{
  "funds": 100.50
}
```
**Response:**
```json
{
   "status": "success",
   "message": "Deposit successful",
   "new_balance": 250.52
}
```

To give a withdraw to customer, send a `POST` request to `/api/accounts/{accountId}/withdraw` with the following payload:

**Request Body:**

```json
{
  "funds": 100.50
}
```
**Response:**
```json
{
   "status": "success",
   "message": "Withdraw successful",
   "new_balance": 200.52
}
```

To do transfer between accounts, send a `POST` request to `/api/accounts/transfer` with the following payload:

**Request Body:**

```json
{
   "from" : 10, //Customer id
   "to" : 2,    //Customer id
   "funds" : 50
}
```
**Response:**
```json
{
   "status": "success",
   "message": "Transfer Successful"
}
```

### Running Tests

```
./vendor/bin/phpunit --testdox
```

