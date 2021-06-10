<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserValidationRequest;
use App\Http\Requests\PasswordChangeValidationRequest;
use App\Models\User;
use App\Http\Resources\ContactResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Events\AccountDeleted;

class UsersController extends Controller
{
    /**
     * @param UserValidationRequest $request
     * @return ContactResource
     */
    public function update(UserValidationRequest $request){
        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->setProfileImage($request->profile_image);
        $user->save();
        return new ContactResource($user);
    }

    /**
     * @param PasswordChangeValidationRequest $request
     * @return Response (json)
     */
    public function updatePassword(PasswordChangeValidationRequest $request){

        auth()->user()->fill([
            'password' => Hash::make($request->password)
        ])->save();

        return response()->json(['message' => 'Password Updated Successfuly']);
    }

    /**
     * @param Request $request
     * @return Response (json)
     */
    public function delete(Request $request){
        $user = auth()->user();
        $userConnections = $user->getAllConnections();

        broadcast(new AccountDeleted($user, $userConnections))->toOthers(); 

        $user->deleteAccount();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $redirect = url("/login");
        return response()->json(['message' => 'Account Deleted', 'redirect_url' => $redirect]);
    }
}
