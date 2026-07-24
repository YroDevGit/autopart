<?php //route: customer/add

//Add codes here...
use Classes\Response;
use Classes\Request;
use Tables\Customer;
use Classes\DB;

$email = Request::post("email");
$code = Request::post("code");

//$data = Customer::fuzzy(["fullname"=> "tyrnelmalocon"]);
$data = DB::fuzzy("customer",["fullname"=> "tyrnelmalocon"]);

Response::code(200)->data($data)->send();


