<?php 
namespace Tables;
use Classes\BaseTable;

class Logs extends BaseTable {
    
    protected $table = "logs";

    protected $primaryKey = "id";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>