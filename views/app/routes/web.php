<?php
use Tables\User;
use Classes\Ccookie;
use Classes\Page;
use Classes\Ctrx;

Page::group(
"cashier/*"
)->except("cashier/orders", "cashier/pos")->run(function(){
    if(! Ctrx::get_user_data("id")){
        redirect("logout");
      }
      $user = User::findOne(["id"=> Ctrx::get_user_data("id")]);
      if(! $user){
        redirect("logout");
      }
      if(isset($user['role']) && ( $user['role'] != 1)){
        redirect("logout");
      }
      $fullname = $user['fullname'] ?? "USER";
      
      gval("fullname", $fullname);  
      gval("role", $user['role']);      
});

Page::group(
  "cashier/*"
  )->run(function(){
      if(! Ctrx::get_user_data("id")){
          redirect("logout");
        }
        $user = User::findOne(["id"=> Ctrx::get_user_data("id")]);
        if(! $user){
          redirect("logout");
        }
        if(isset($user['role']) && ($user['role'] != 2 && $user['role'] != 1)){
          redirect("logout");
        }
        $fullname = $user['fullname'] ?? "USER";
        
        gval("fullname", $fullname);  
        gval("role", $user['role']);      
  });

Page::group("/*");
