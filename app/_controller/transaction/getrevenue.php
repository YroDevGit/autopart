<?php //route: transaction/getRevenue

//Add codes here...

use Classes\DB;
use Classes\Request;
use Classes\Response;

$year = Request::post("year");
if(! $year){
    Response::code(404)->message("Year not found.!")->send();
}

$arr = ["01", "02", "03", "04", "05", "06", "07","08","09","10","11","12"];
$ret = [];

foreach($arr as $k=>$v){
    $yearmonth=$year."-".$v;
    $res = DB::query("SELECT sum(t.subtotal) 'subtotal', SUM(t.shipping)'shipping' FROM transaction t WHERE t.created_at LIKE '%$yearmonth%'");
    $data = $res[0] ?? [];
    $ret[] = ($data['subtotal'] ?? 0) + ($data['shipping'] ?? 0);
}

Response::code(200)->data($ret)->send();