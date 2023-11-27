<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
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

}
