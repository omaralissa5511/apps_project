<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class MUser_Group extends Model
{
    use HasFactory;
    protected $table ='user_group';
    protected $fillable = ['user_id','group_id'];
}
