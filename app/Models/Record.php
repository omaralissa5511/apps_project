<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;
    protected $connection = 'mysql-2';
    protected $fillable=['type','user_id','file_id'];
    protected $table = 'records';

    public function users () {
        return $this->belongsToMany(User::class);
    }

    public function files () {
        return $this->belongsToMany(File::class);
    }
}
