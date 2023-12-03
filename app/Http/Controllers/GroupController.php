<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\User_Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class GroupController extends Controller
{
    public function create_group(Request $request){


        $request->validate([
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $request->file('image')->store('group', 'public');

        $group = Group::create([
            'name' => $request->input('name'),
            'image' => $imagePath,
        ]);

        return response()->json(['message' => 'Group created successfully', 'group' => $group]);
    }


    public function getAllGroups(){
        $group=Group::all();
        if($group)
            return response()->json($group);
        return response()->json("failed");

    }

    public function deleteGroup(Request $request){
        $delete =Group::find($request->id);
        if($delete) {
            $delete->delete();
            return response()->json("delete group successfully");
        }
        return response()->json("failed");
    }


    public function addUserToGroup($groupID,$userID)
    {
        $group = Group::find($groupID);
        $user = User::find($userID);

        if ($group && $user) {

            $exist = User_Group::where('user_id',$userID);

            if ($exist)

                return response()->json(["message" => "المستخدم$user->name موجود مسبقا "]);
             User_Group::create([
                 'user_id' => $userID,
                 'group_id' => $groupID
             ]);
            return response()->json(["message" => " تمت إضافة المستخدم  $user->name إلى المجموعة بنجاح "]);

            }else {
                    return response()->json(["message" => "المستخدم او الغروب غير موجود"]);
                  }

    }


    public function deleteUserFromGroup(Request $request)
    {
        $group = Group::find($request->gid);
        if ($group && Auth::id() == $group->admin_id) {
            $user = User::where('id', $request->uid)->first();
            if ($user) {
                $exist = $group->users()->find($user->id);
                if ($exist) {
                    $group->users()->delete($exist->id);
                    return response()->json(["message" => " تم حذف المستخدم بنجاح"]);
                } else
                    return response()->json(["message" => "المستخدم$user->name  غير موجود  في المجموعة "]);
            }
            return response()->json(["message" => "المستخدم غير موجود اطلاقا "]);
        }

        return response()->json(["message" => " أنت لست ادمن"]);


    }

}
