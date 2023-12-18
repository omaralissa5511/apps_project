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


    public function joinOrder (Request $request) {

        $group_id = $request->groupID;
        $user_id = $request->userID;

        $o = Order::where('user_id', $user_id)->Where('group_id', $group_id)->first();

         if($o){
             Log::channel('mysql')->info('User '. $user_id .' ask to join to group '.$group_id.'but he already ask to join',[$user_id]);
             return response()->json(['message' => 'you already ask to join']);
         }
         else{
             $order = Order::create([

                 'user_id' => $user_id,
                 'group_id' => $group_id,
                 'status' => 'pending'
             ]);
             Log::channel('mysql')->info('User '.$user_id.' ask to join to group '.$group_id,[$user_id]);
             return response()->json(['message' => 'your order has waiting to approve']);
         }

    }

    public function approvePendingOrder(Request $request){

        $orderID =  $request->orderID;
        $orderStatus =  $request->orderID;

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
            Log::channel('mysql')->info('User '.$userId .'accepted into the group '.$groupId ,[$userId]);
        }

    }


    public function getPendingOrder ($groupID){
        $order = Order::where('status','pending')->where('group_id',$groupID)
            ->get();
        Log::channel('mysql')->info('Get all orders to join to the group '.$groupID);

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
            Log::channel('mysql')->info('Group '.$group_id.' created successfully by '.$user_id,[$user_id]);

            return response()->json(['message' => 'Group created successfully', 'group' => $group]);
        }
        else {
            Log::channel('mysql')->info('Group '.$group_id.' failed to created by '.$user_id,[$user_id]);

            return response()->json(['message' => 'we got a some shit']);
        }
    }


    public function getAllGroups($userid){

        $group_ids=User_Group::where('user_id',$userid)->get()->pluck('group_id');

        $group = Group::find($group_ids);
        Log::channel('mysql')->info('Get all Groups for user '.$userid ,[$userid]);

        return $group;


    }

    public function get_AllGroups(){

        $group = Group::get();
        Log::channel('mysql')->info('Get All Groups');

        if($group){
            return response()->json($group);
        }else {
            return response()->json(['message'=>'no groups']);
        }



    }

    public function deleteGroup(Request $request){
        $delete =Group::find($request->id);
        if($delete) {
            $delete->delete();
            Log::channel('mysql')->info('Group '.$delete.' deleted successfully by ');

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
