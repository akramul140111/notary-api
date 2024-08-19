<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    //

    public function profileEdit($id)
    {
        $userInfo = User::where('id',$id)->first();
        return response()->json([
            'userData' => new UserResource($userInfo),
            'signature' => $userInfo->signature ? Storage::url($userInfo->signature) : $userInfo->signature
        ],200);
    }

    public function profileUpdate(Request $request, $id)
    {
        try {
            if ($request->hasFile('signature')) {
                $file = $request->file('signature');
                $path = $file->store("profile", 'public');
    
                User::where('id', $id)->update([
                    'signature' => $path,
                ]);
            }
    
            $userInfo = User::find($id);
            return response()->json([
                'userData'  => $userInfo,
                'signature' => $userInfo->signature ? Storage::url($userInfo->signature) : null,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => "Error occurred"], 400);
        }
    }
}
