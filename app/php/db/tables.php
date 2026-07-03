<?php
use Classes\Migration;

Migration::table_ts("logs", [
    "id" => "@primary",
    "message" => ["varchar"=>800],
    "status" => ["int"=>11, "default" => 1]
]);

Migration::table_ts("user", [
    "id" => "@primary",
    "emp_id" => ["varchar" => 10],
    "username" => "varchar",
    "password" => "varchar",
    "fullname" => "varchar",
    "role" => "int"
], true);

Migration::table_ts("role", [
    "id" => "@pk",
    "role" => "varchar",
    "details" => "varchar"
], true);

Migration::table_ts("product", [
    "id" => "@pk",
    "name" => "varchar",
    "details" => "varchar",
    "price" => "float",
    "category" => INTEGER,
    "image" => "text",
    "added_by" => INTEGER
], true);

Migration::table_ts("inventory", [
    "id" => PK,
    "product_id" => INTEGER,
    "quantity" => INTEGER,
    "stock_id" => VARCHAR,
    "supplier_id" => INTEGER 
], true);

Migration::table_ts("category", [
    "id" => PK,
    "name" => VARCHAR,
    "details" => VARCHAR
],true);

Migration::table_ts("supplier", [
    "id"=> PK,
    "name" => VARCHAR,
    "address" => VARCHAR,
    "contact" => VARCHAR
], true);


Migration::table_ts("customer", [
    "id" => PK,
    "fullname" => VARCHAR,
    "contact" => VARCHAR,
    "address" => VARCHAR,
    "fulladdress"=> VARCHAR,
    "username" => VARCHAR,
    "password" => VARCHAR,
    "email" => VARCHAR
], true);


Migration::table_ts("verification", [
    "id"=> PK,
    "email" => "varchar",
    "code" => ["varchar"=> 10000]
], true);

Migration::table_ts("transaction_details", [
    "id" => PK,
    "product_id" => INTEGER,
    "customer_id" => INTEGER,
    "quantity" => INTEGER,
    "price" => FLOAT,
    "total_price" => FLOAT,
    "transaction_code" => VARCHAR
], true);

Migration::table_ts("transaction", [
    "id" => PK,
    "transaction_code" => VARCHAR,
    "subtotal" => FLOAT,
    "shipping" => FLOAT,
    "total_price" => FLOAT,
    "customer_id" => INT,
    "status" => ["int"=>11, "default"=>0],
    "remarks" => VARCHAR,
    "rider" => INTEGER,
    "date_delivered" => DATETIME
], true);

Migration::table_ts("address", [
    "id" => PK,
    "city" => VARCHAR,
    "brgy" => VARCHAR,
    "shipping" => FLOAT,
    "city_code" => VARCHAR,
    "brgy_code" => VARCHAR,
    "estimated" => INTEGER
], true);

Migration::table_ts("photo", [
    "id" => PK,
    "path" => TEXT,
    "alt" => VARCHAR,
    "size" => FLOAT,
    "realpath" => TEXT
], true);

Migration::table_ts("configs", [
    "id"=> PK,
    "title" => VARCHAR,
    "string" => VARCHAR
], true);
