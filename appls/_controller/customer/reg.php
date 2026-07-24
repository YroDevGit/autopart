<?php //route: customer/reg

//Add codes here...

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

$check = Customer::findOne(["or" => ["email" => $email, "username" => $email]]);

if ($check) {
    Response::code(402)->message("Email already exist")->send();
}

DB::bundle(function () use($email) {
    $rand = Random::text(16);
    $hash = Hash::encrypt($email);
    $NewHash = $rand . $hash;

    $link = rootpath . "/verify?code=" . $NewHash;

    Verification::insert([
        "email" => $email,
        "code" => $NewHash
    ]);

    Mail::send_email($email, variable("appname") . " Registration", "Please complete registration @ <a href='$link'>click here</a>");
});

Response::code(200)->send();
