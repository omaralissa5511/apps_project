<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Group;
use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FileController extends Controller
{

    public function check_in($fileId) {

        $fil = File::findOrFail($fileId);

        if(!$fil) return 'the file is not exist';
        if($fil->status!=0){

            $lockKey = "file_lock_{$fileId}";

            $lock = Cache::lock($lockKey, 30);
            $lockAcquired = $lock->get();
            if ($lockAcquired) {

                try {
                    $file = File::findOrFail($fileId);
                    $file->status = 0;
                    $file->save();

                    $userID = Auth::id();
                    Record::query()->create([
                        'user_id' => $userID,
                        'file_id' => $fileId,
                        'type' => 'Check-IN'
                    ]);

                }
                finally {
                    $lock->release();
                }
                return
                    response()->json(['message' => 'File updated successfully']);
            }
        }

        else {
            return response()->json(['message' => 'Unable to acquire the file lock. Try again later.'], 403);
        }
    }


    public function check_out($fileId){

        $userID = Auth::id();
        $file = File::findOrFail($fileId);

        if(!$file) return 'the file is not exist';
        if($file->status!=0) {
            return response()->json(['message' => 'the file is  available']);
        }
        $user_id = Record::where('file_id','=',$fileId)->first()->user_id;

        if($userID==$user_id){
            $file->status = 1;
            $file->save();

            Record::query()->create([
                'user_id' => $userID,
                'file_id' => $fileId,
                'type' => 'Check-OUT'
            ]);
            return response()->json(['message' => 'the file has been free']);
        } else {
            return response()->json(['message' => 'you can not free the file']);
        }

    }







    public function createFile(Request $request)
    {
        $file = $request->validate([
            'name' => 'required',
            'path' => 'required'
        ]);
    }

    public function reserveFile( $id)
    {

        $cacheKey = 'file_reservation_lock_' . $id;

        if (Cache::add($cacheKey, true, 10)) {

            try {
                $file = File::findOrFail($id);


                if (!$file->user_id) {

                    $file->update(['user_id' => auth()->user()->id, 'status' => true]);

                    return response()->json(['message' => 'File reserved successfully']);
                } else {
                    return response()->json(['message' => 'File is already reserved'], 400);
                }
            } finally {
                Cache::forget($cacheKey);
            }
        } else {
            return response()->json(['message' => 'Another process is currently reserving the file'], 400);
        }
    }

    public function cancelReservation( $id)
    {
        $cacheKey = 'file_reservation_lock_' . $id;

        if (Cache::add($cacheKey, true, 10)) {
            try {
                $file = File::findOrFail($id);

                if ($file->user_id === auth()->user()->id) {
                    $file->update(['user_id' => null, 'status' => false]);
                    return response()->json(['message' => 'Reservation canceled successfully']);
                } else {
                    return response()->json(['message' => 'You are not the owner of the reservation'], 400);
                }
            } finally {
                Cache::forget($cacheKey);
            }
        } else {
            return response()->json(['message' => 'Another process is currently canceling the reservation'], 400);
        }
    }

    public function get_groupFile ($id) {

        $r = File::findOrFail($id);
        $files = File::query()->where('group_id',$id)->get();
        return $files;
    }

    public function test ($id){
        $file = File::findOrFail($id);

        if($file->status==0)
            return "you can not update file";
            $file -> status =7;
            $file->save();
            return $file;


    }
}