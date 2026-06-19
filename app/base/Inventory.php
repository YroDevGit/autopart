<?php 
namespace Tables;
use Classes\BaseTable;

class Inventory extends BaseTable {
    
    protected $table = "inventory";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>