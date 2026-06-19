<?php //route: user/login

//Add codes here...

use Classes\Ccookie;
use Classes\Ctrql;
use Classes\Ctrx;
use Classes\Random;
use Classes\Response;
use Classes\Validator;
use Tables\User;

$email = Validator::post("email")->required()->label("Email")->email()->exec();
$password = Validator::post("password")->required()->exec();

if(Validator::failed()){
    $errors = Validator::errors();
    Response::code(402)->message("Validation failed")->errors($errors)->send();
}

$res = User::findOne(["username"=>$email]);

if(! $res){
    Response::code(404)->message("User not found")->send();
}

$pass = $res['password'];

if($password !== $pass){
    Response::code(404)->message("Incorrect password for $email")->send();
}

Ctrql::activate("CRUDQ",);
$cookie = Random::string(20);

Ccookie::add("cookie", $cookie);

if(! $res['id']){
    Response::code(404)->message("User Error")->send();
}
Ccookie::add("user", $res['id']);
Ctrx::set_user_data([
    "id" => $res['id'],
    "role" => $res['role']
]);


Response::code(200)->message("OK")->var(["cookie"=>$cookie, "role"=>$res['role'], "userid"=>$res['id']])->send();