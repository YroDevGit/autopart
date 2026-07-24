<?php 
namespace Tables;
use Classes\BaseTable;

class Transaction_details extends BaseTable {
    
    protected $table = "transaction_details";

    protected $primaryKey = "id";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>