<?php //route: user/forgotpass
use Classes\DB;
use Classes\Hash;
use Classes\Mail;
use Classes\Random;
use Classes\Request;
use Classes\Response;
use Tables\Customer;
use Tables\Verification;

$email = Request::post("email");
if (! $email) {
    Response::code(402)->message("email is required")->send();
}

if(str_contains($email, "+")){
    Response::code(402)->message("Your email looks a dump email")->send();
}

if(! str_contains($email,"gmail.com") && ! str_contains($email,"outlook.com")){
    Response::code(402)->message("Invalid Email address")->send();
}

$check = Customer::findOne(["or" => ["email" => $email, "username" => $email]]);

if (! $check) {
    Response::code(402)->message("User email not exist.!")->send();
}

DB::bundle(function () use($email) {
    $rand = Random::integer(6);
    

    Customer::update(["email"=>$email],["password"=>$rand]);

    Mail::to($email)
    ->subject(variable("appname") . " Registration")
    ->message("Your new password is $rand")
    ->send();
});

Response::code(200)->send();

