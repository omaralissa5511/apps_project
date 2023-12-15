<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class MRecord extends Model
{
    use HasFactory;
    protected $fillable=['type','user_id','file_id'];
    protected $table = 'records';

    public function Musers () {
        return $this->belongsToMany(MUser::class);
    }

    public function Mfiles () {
        return $this->belongsToMany(MFile::class);
    }
}
