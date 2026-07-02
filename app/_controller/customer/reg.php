<?php //route: customer/reg

//Add codes here...
use Classes\Request;
use Classes\Response;
use Tables\Customer;

$email = Request::post("email");

if(! $email){
    Response::code(402)->message("email is required")->send();
}

$check = Customer::findOne(["or"=>[["email"=>$email],["username"=>$email]]]);

if($check){
    Response::code(402)->message("Email already exist")->send();
}

Response::code(200)->send();

