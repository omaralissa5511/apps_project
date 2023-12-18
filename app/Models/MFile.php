<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class MFile extends Model
{
    use HasFactory;
    protected $fillable=['name','status','group_id','user_id','created_at','updated_at'];

    protected $guarded = [];
    protected $table = 'files';

    public function Muser()
    {
        return $this->belongsTo(MUser::class);
    }

    public function Mgroup()
    {
        return $this->belongsTo(MGroup::class);
    }
    public function Mrecords(){
        return $this->hasMany(MRecord::class);
    }
}
