<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Record;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller {

    public function getUsers ()
    {
        $user = User::get();
        return $user;
    }


}
