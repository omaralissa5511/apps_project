<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Group extends Model
{
    use HasFactory;
    protected $connection = 'mysql-2';
    protected $table ='user_group';
    protected $fillable = ['user_id','group_id'];
}
