<?php 
namespace Tables;
use Classes\BaseTable;

class Product extends BaseTable {
    
    protected $table = "product";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = true;
}
?>