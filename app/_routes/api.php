<?php

use Classes\Ctrx;
use Classes\Response;
use Classes\Router;

Router::group(
    ["post"=>"user/login"]
);

Router::group(
    ["post" => "transaction/getupdate"],
    ["get" => "user/add"],
)->run(function(){
    if(! Ctrx::has_user_data()){
        Response::code(unauthorized_code)->message("User not authorized")->send(unauthorized_code);
    }
});
