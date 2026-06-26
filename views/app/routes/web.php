<?php
use Tables\User;
use Classes\Ccookie;
use Classes\Page;

Page::group(
"cashier/*"
)->run(function(){
    if(! Ccookie::get("user")){
        redirect("logout");
      }
      $user = User::findOne(["id"=> Ccookie::get("user")]);
      if(! $user){
        redirect("logout");
      }
      if(isset($user['role']) && $user['role'] != 2){
        redirect("logout");
      }
      $fullname = $user['fullname'] ?? "USER";
      
      gval("fullname", $fullname);      
});