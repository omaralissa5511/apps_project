<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $connection = 'mysql-2';
    protected $fillable=['name','image','admin','created_at'];
    protected $table='groups';


    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function files(){
        return $this->hasMany(File::class);
    }
}
