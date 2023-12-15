<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class MGroup extends Model
{
    use HasFactory;
    protected $fillable=['name','image','admin','created_at'];
    protected $table='groups';


    public function Musers(){
        return $this->belongsToMany(MUser::class);
    }

    public function Mfiles(){
        return $this->hasMany(MFile::class);
    }
}
