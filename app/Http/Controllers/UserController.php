<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Group;
use App\Models\Record;
use App\Models\User;
use App\Models\User_Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller {

    public function getUsers ($group_id)
    {

        $group = Group::findOrFail($group_id);

        $userIDs = User_Group::where('group_id',$group_id)->get()->pluck('user_id');

        $user = User::find($userIDs);

        return $user;
    }


}
