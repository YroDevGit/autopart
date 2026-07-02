<?php //route: transaction/getUpdate

//Add codes here...

use Classes\DB;
use Classes\Request;
use Classes\Response;

$arr = Request::post("arr");
$arr = json_decode($arr, true);

$data = [];

foreach($arr as $k=>$v){
    $res = DB::query("SELECT c.id 'customer', c.fullname, c.contact, c.address, t.created_at,t.transaction_code FROM transaction t, customer c WHERE t.customer_id = c.id and c.contact != '-' AND t.id = ?", [$v]);
    if($res && isset($res[0])){
        $data[] = $res[0];
    }
}


$rp = DB::query("SELECT c.id 'customer', t.id 'transaction', c.fullname, c.contact, c.address, t.created_at,t.transaction_code FROM transaction t, customer c WHERE t.customer_id = c.id and c.contact != '-' order by t.created_at asc");

$newData=[];

foreach($rp as $kk=>$row){
  $id = $row['transaction'];
  if(in_array($id, $arr)){
    $newData[] = [...$row, "isNew"=>"yes"];
  }else{
    $newData[] = [...$row, "isNew"=>"no"];
  }
}

Response::code(200)->data($newData??[])->var(["arr"=>$arr])->send();
