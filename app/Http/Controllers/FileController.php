<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Group;
use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use ITelmenko\Logger\Laravel\Models\Log;

class FileController extends Controller
{

    public function getFileRecord ($file_id,$userID) {

        $record = Record::where('file_id',$file_id)->where('user_id',$userID)->get();

        if(count($record) != 0) {
            Log::channel('mysql')->info('Get all Records for user '.$userID ,[$userID]);
                 return $record;
        }else {
            Log::channel('mysql')->info($userID." don't have access on the file ". $file_id,[$userID]);
            return response()->json(['message'=>' U do not have access on this file']);
        }
    }


    public function addFile (Request $request){

        $status = $request->status;
        $name = $request->name;
        $group_id = $request->groupID;
        $user_id = $request->userID;

        $file = File::create([
            'name' => $name,
            'status' => $status,
            'group_id' => $group_id,
            'user_id' => $user_id
        ]);

        if($file) {
            Log::channel('mysql')->info($user_id .'added file'.$name  ,[$user_id]);
            return "yep";
        }
    }



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
                Log::channel('mysql')->info('Check in file '.$file. 'by' .$userID ,[$userID]);
                return response()->json(['message' => 'File updated successfully']);
            }
        }

        else {
            Log::channel('mysql')->info('Unable to acquire the file lock '.$fileId ,[Auth::id()]);
            return response()->json(['message' => 'Unable to acquire the file lock. Try again later.'], 403);
        }
    }





    public function get_groupFile ($id) {

        $r = File::where('group_id','=',$id)->first();

        Log::channel('mysql')->info('Get all files in group ');
        if($r){
            $files = File::where('group_id','=',$id)->get();
            return $files;
        }
        else { return response()->json(['message' => 'no group found']);}

    }


    public function check_in_m(Request $request ) {

        $data = json_decode($request->getContent(), true);

        if (!isset($data['Ids']) || !is_array($data['Ids'])) {
            return response()->json(['message' => 'Invalid input.'], 400);
        }

        $IDS = $data['Ids'];

        foreach ($IDS as $id) {
            if (!File::find($id)) {

                return response()->json(['message' => 'One or more files do not exist.'], 404);
            }
        }

        $files = File::find($IDS);

            foreach ($files as $file) {
                if ($file->status == 0) {
                    return response()->json(['message' => "File is already reserved."], 403);
                }
            }

            foreach ($files as $file) {
             if ($file->status != 0) {
                $lockKey = "file_lock_{$file->id}";

                $lock = Cache::lock($lockKey, 30);
                $lockAcquired = $lock->get();

                if ($lockAcquired) {
                    try {
                        $file->status = 0;
                        $file->save();

                        $userID = Auth::id();
                        Record::query()->create([
                            'user_id' => $userID,
                            'file_id' => $file->id,
                            'type' => 'Check-IN'
                        ]);
                    } finally {
                        $lock->release();
                    }
                } else {
                    // في حالة فشل الحصول على قفل الملف
                    return response()->json(['message' => "Unable to acquire the file lock for file {$file->id}. Try again later."], 403);
                }
            } else {
                // في حالة أن الملف محجوز بالفعل
                return response()->json(['message' => "File {$file->id} is already reserved."], 403);
            }
        }

        return response()->json(['message' => 'Files updated successfully']);
    }


    public function check_out(Request $request ){

        $data = json_decode($request->getContent(), true);

         $ID = $data['Ids'];
         $USER = $data['userID'];

         $file = File::findOrFail($ID);

        if(!$file) return 'the file is not exist';
        if($file->status!=0) {
            return response()->json(['message' => 'the file is  available']);
        }
        $user = Record::where('file_id','=',$ID)->latest()->first();

       $user_id = $user ->user_id;
        if($USER==$user_id){
            $file->status = 1;
            $file->save();

            Record::query()->create([
                'user_id' => $USER,
                'file_id' => $ID,
                'type' => 'Check-OUT'
            ]);
            return response()->json(['message' => 'the file has been free']);
        } else {
            return response()->json(['message' => 'you can not free the file']);
        }

    }

    public function getfile(){

       return File::all();
    }


}
