<?php 
namespace Tables;
use Classes\BaseTable;

class Customer extends BaseTable {
    
    protected $table = "customer";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = true;
}
?>