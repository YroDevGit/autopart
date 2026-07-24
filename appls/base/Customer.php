<?php 
namespace Tables;
use Classes\BaseTable;

class Customer extends BaseTable {
    
    protected $table = "customer";

    protected $primaryKey = "id";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>