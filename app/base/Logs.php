<?php 
namespace Tables;
use Classes\BaseTable;

class Logs extends BaseTable {
    
    protected $table = "logs";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>