<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Group;
use App\Models\Order;
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

    public function deleteUserFromGroup ($dId,$id,$GROUPID) {

        if($id==$dId) {
            return response()->json(["message" => "you can not delete your self"]);
        }

        $dUser  = User_Group::where('user_id',$dId)->where('group_id',$GROUPID)->first();
        $orderD  = Order::where('user_id',$dId)->where('group_id',$GROUPID)->first();
        if($dUser) {
            $dUser -> delete($dId);
            $orderD -> delete($dId);
            return response()->json(["message" => "تم حذف المستخدم"]);
        } else {
            return response()->json(["message" => " NO USER FOUND "]);
        }

    }


}
