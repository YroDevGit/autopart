<?php 
namespace Tables;
use Classes\BaseTable;

class Transaction extends BaseTable {
    
    protected $table = "transaction";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = true;
}
?>