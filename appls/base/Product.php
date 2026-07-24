<?php 
namespace Tables;
use Classes\BaseTable;

class Product extends BaseTable {
    
    protected $table = "product";

    protected $primaryKey = "id";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>