<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Order;
use App\Models\User;
use App\Models\User_Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class GroupController extends Controller
{


    public function get_Group_Users ($groupID) {

        $usersID = User_Group::where('group_id',$groupID)->get()->pluck('user_id');

        $users = User::find($usersID);
        return $users;

    }



    public function joinOrder (Request $request) {

        $group_id = $request->groupID;
        $user_id = $request->userID;


        $userGroup = User_Group::where('group_id',$group_id)->where('user_id',$user_id)->first();

         if($userGroup) {
             return response()->json(['message' => 'you already joined this group']);
         }

        $o = Order::where('user_id', $user_id)->Where('group_id', $group_id)->first();

         if($o){
             return response()->json(['message' => 'you already ask to join']);
         }
         else{
             $order = Order::create([

                 'user_id' => $user_id,
                 'group_id' => $group_id,
                 'status' => 'pending'
             ]);
             return response()->json(['message' => 'your order has waiting to approve']);
         }

    }

    public function approvePendingOrder(Request $request){

        $orderID =  $request->orderID;
        $order = Order::where('id',$orderID)->first();
        $userId = Order::where('id',$orderID)->first()->user_id;
        $groupId = Order::where('id',$orderID)->first()->group_id;

        if($order){
            $order -> status = "accepted";
            $order->save();

            User_Group::create([
                 'group_id'=>$groupId,
                 'user_id'=>$userId
            ]);
        }

    }



    public function declinePendingOrder(Request $request){

        $orderID =  $request->orderID;
        $order = Order::where('id',$orderID)->first();
        $order -> delete();

    }


    public function getPendingOrder ($groupID){

        $order = Order::where('status','pending')->where('group_id',$groupID)
            ->get();
        if($order) {
            return $order;
        }else {
            return response()->json(['no pending orders']);
        }

    }

    public function create_group(Request $request)
    {

        $user_id = $request -> admin;

        $admin = User::where('id',$user_id)->first()->name;

        $request->validate([
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,jpg_large,gif|max:2048',
        ]);

        $imagePath = $request->file('image')->store('group', 'public');

        $group = Group::create([
            'name' => $request->input('name'),
            'admin' => $admin,
            'image' => $imagePath,
        ]);

        $group_id = $group->id;


        User_Group::create([
            'group_id' => $group_id,
            'user_id' => $user_id
        ]);

        if ($group) {
            return response()->json(['message' => 'Group created successfully', 'group' => $group]);
        }
        else {
            return response()->json(['message' => 'we got a some shit']);
        }
    }


    public function getAllGroups($userid){

        $group_ids=User_Group::where('user_id',$userid)->get()->pluck('group_id');

        $group = Group::find($group_ids);

            return $group;


    }

    public function get_AllGroups(){

        $group = Group::get();
        if($group){
            return response()->json($group);
        }else {
            return response()->json(['message'=>'no groups']);
        }



    }

    public function deleteGroup($GID , $UID){

        $group_Admin =Group::where('id',$GID)->first()->admin;

         $userID_OwnerGruop = User::where('name',$group_Admin)->first()->id;

         if($UID == $userID_OwnerGruop){
             $group = Group::find($GID);
             $group->delete();
             return response()->json("GROUP DELETED SUCCESSFULLY");
         }
         else{
              return response()->json("U R NOT THE OWNER OF THE GROUP");
         }
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


    public function delete_User_From_Group(Request $request)
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
