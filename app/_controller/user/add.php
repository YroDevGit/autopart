<?php //route: user/add

//Add codes here...

use Classes\Mail;
use Classes\Random;
use Classes\Response;
use Classes\Validator;
use Tables\User;

$fullname = Validator::post("fullname")->required()->maxChars(80)->exec();
$email = Validator::post("email")->email()->required()->exec();
$role = Validator::post("role")->required()->number()->in([1,2,3])->exec();
$active = Validator::post("status")->required()->number()->in([1,0])->exec();

if($errs = Validator::errors()){
    Response::code(401)->errors($errs)->send();
}

$check = User::findOne(["username"=> $email]);
if($check){
    Response::code(402)->message("Email already taken")->send();
}

$pass = Random::integer(6);

User::insert([
    "fullname"=>$fullname,
    "username" => $email,
    "role" => $role,
    "active" => $active,
    "password" => $pass
]);

Mail::send_email($email, "KYG Autoparts", "Your account has been created, password is: $pass");

Response::code(200)->message("OK")->send();
