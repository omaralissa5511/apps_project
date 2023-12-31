<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $connection = 'mysql-2';
    protected $fillable=['name','status','group_id','user_id','created_at','updated_at'];

    protected $guarded = [];
    protected $table = 'files';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function records(){
        return $this->hasMany(Record::class);
    }
}
