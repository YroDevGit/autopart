<?php //route: transaction/pos

//Add codes here...


use Classes\Mail;
use Classes\Random;
use Classes\Request;
use Classes\Response;
use Classes\Validator;
use Tables\Customer;
use Tables\Transaction;
use Tables\Transaction_details;
use Tables\User;
use Tables\Product;

$subtotal = Validator::post("subtotal")->required()->number()->collect('subtotal')->exec();
$shipping = Validator::post("shippingFee")->required()->number()->collect('shippingFee')->exec();
$code = Validator::post("code")->required()->collect('code')->exec();
$total = Validator::post("total")->required()->number()->collect('total')->exec();

if (Validator::failed()) {
    $errors = Validator::errors();
    Response::code(401)->message("Validation failed")->errors($errors)->send();
}

$cart = Request::post("cart");

db_start();
try {
    $pass = Random::integer(4);
    $cust = Customer::insert([
        "fullname" => "AUT_". date("ymdHis"),
        "contact" => "-",
        "address" => "kyg@gmail.com",
        "fulladdress" => "-",
        "username" => "-",
        "password" => "",
        "email" => ""
    ]);

    $findCode = Transaction::findOne(["transaction_code"=>$code]);
    if($findCode){
        db_rollback();
        Response::code(207)->message("Saving order error, please try again")->send();
    }

    $tr_id = Transaction::insert([
        "transaction_code" => $code,
        "subtotal" => $subtotal,
        "shipping" => 0,
        "total_price" => $total,
        "customer_id" => $cust->_id(),
        "status" => 11
    ]);

    foreach ($cart as $k => $v) {
        $row = $cart[$k];
        $qty = $row['quantity'];
        $prod_id = $row['id'];
        $price = $row['price'];

        $tr_dt_id = Transaction_details::insert([
            "product_id" => $prod_id,
            "customer_id" => $cust->_id(),
            "quantity" => $qty,
            "price" => $price,
            "total_price" => $price * $qty,
            "transaction_code" => $code
        ]);
    }
    $url = rootpath;
    $appname = variable("appname");
    db_commit();
} catch (Throwable $e) {
    db_rollback();
    Response::code(500)->message($e->getMessage())->var(["e" => $e->getTrace()])->send();
}

Response::code(200)->message("ok")->var(['userid' => $cust->_id(), 'ref'=>$code])->send();
