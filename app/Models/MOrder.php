<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class MOrder extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = ['group_id','user_id','status'];
}
