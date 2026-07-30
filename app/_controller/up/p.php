<?php //route: up/p

//Add codes here...

use Classes\CtrStorage;
use Classes\File;
use Classes\Response;

$up = CtrStorage::upload_file("pict");

Response::code(200)->message("OK")->var(["aw"=>$up])->send();
