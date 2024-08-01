<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile(User $user)
    {
        $user = auth()->user();

        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    public function editprofile(Request $request)
    {
        $data = $request->validate([
            'avatar' => 'nullable',
            'username' => 'required|string|max:20',
            'name' => 'required|string|max:50',
            'email' => 'required|email',
            'phone_number' => 'nullable|string|max:255',
            'domicile' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'pronouns' => 'nullable|string|max:255',
            'bio' => 'nullable'
        ]);

        // Avatar
        if ($request->hasFile('avatar')) {
            $avatarName = time() . '-' . auth()->user()->username . '.' . $request->avatar->getClientOriginalExtension();
            $request->avatar->move(public_path('avatars'), $avatarName);
            $data['avatar'] = $avatarName;
        }

        // Username Unique
        $existingUsername = User::where('username', $data['username'])->where('id', '<>', auth()->id())->first();
        if ($existingUsername) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username Already Exists',
                'data' => null
            ], 422);
        }

        // Email Unique
        $existingEmail = User::where('email', $data['email'])->where('id', '<>', auth()->id())->first();
        if ($existingEmail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email Already Exists',
                'data' => null
            ], 422);
        }

        $user = User::find(auth()->id());
        $user->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile Edited Successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function changepassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);

        $user = User::findOrFail(auth()->id());

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kata sandi saat ini salah',
                'data' => null
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password Edited Successfully',
            'data' => new UserResource($user)
        ]);
    }
}
