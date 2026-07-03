<?php //route: customer/add

//Add codes here...
use Classes\Response;
use Classes\Request;
use Tables\Customer;

$email = Request::post("email");
$code = Request::post("code");


