<?php //route: product/add

//Add codes here...

use Classes\Ccookie;
use Classes\Request;
use Classes\Response;
use Tables\Product;
use Classes\Validator;

if(! Ccookie::get("user")) Response::code(ctrql_auth_failed)->message("User is not authenticated to add products")->send(ctrql_auth_failed);

$name = Validator::post("name")->label("Name")->required()->maxChars(100)->X();
$price = Validator::post("price")->label("Price")->required()->number()->X();
$details = Validator::post("details")->label("Details")->required()->maxChars(200)->X();
if(Request::post("image")){
    $img = Request::post("image");
    if(str_starts_with($img, "/") || str_starts_with($img, "\\")){
        $image = Validator::post("image")->label("Image")->maxChars(5000)->X();
    }else{
        $image = Validator::post("image")->label("Image")->maxChars(5000)->url()->X();
    } 
}

$category = Validator::post("category")->required()->label("Category")->maxChars(100)->X();

if (Validator::failed()) {
    $errors = Validator::errors();
    Response::code(402)->errors($errors)->send();
}

$check = Product::findOne(["name"=>$name]);
if($check) Response::code(401)->message("Product name already exist")->send();

$id = Product::insert([
    "name" => $name,
    "details" => $details,
    "price" => $price,
    "category" => $category,
    "image" => $image,
    "added_by" => Ccookie::get("user")
]);

Response::code(200)->message("OK")->var(["id"=>$id])->send();
