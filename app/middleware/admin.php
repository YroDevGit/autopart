<?php
use Classes\Ctrx;
//Middleware: admin

if(! Ctrx::has_user_data()){
    Response::code(unauthorized_code)->message("User not authorized")->send(unauthorized_code);
}
