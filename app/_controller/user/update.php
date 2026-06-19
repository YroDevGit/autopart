<?php //route: user/update

//Add codes here...

use Classes\Ccookie;
use Classes\Ctrx;
use Classes\Request;
use Classes\Response;
use Classes\Validator;
use Tables\User;

$id = Ctrx::get_user_data("id");

$fullname = Validator::post("fullname")->required()->exec();
$password = Validator::post("password")->required()->minChars(8)->exec();
$rpassword = Validator::post("rpassword")->required()->label("Re-enter password")->exec();

if(! $password && ! $rpassword){
    if(! $fullname){
        Response::code(401)->message("Fullname should not be empty")->send();
    }
    $where = ["id"=> $id];
    $set = ["fullname" => $fullname];

    User::update($where, $set);
    Response::code(200)->message("OK")->send();
}
if($err = Validator::errors()){
    Response::code(402)->errors($err)->send();
}

if($password !== $rpassword){
    Response::code(401)->message("Password not matched")->send();
}

$where = ["id"=> $id];
$set = ["password" => $password, "fullname" => $fullname];

User::update($where, $set);

Response::code(200)->message("OK")->send();