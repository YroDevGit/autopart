<?php 
namespace Tables;
use Classes\BaseTable;

class User extends BaseTable {
    
    protected $table = "user";

    protected $primaryKey = "id";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>