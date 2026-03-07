<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Helpers\MiaHelper;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class ProfileController extends Controller
{
    use ApiResponse;
    public function profileInfo()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->errorResponse([], 'Unauthorized', 403);
        }

        if ($user->role == 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $response = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'username' => $user->username,
            'avatar' => $user->avatar ? asset($user->avatar) : asset('user.jpg'),
            'role'   => $user->role,
            'address'   => $user->address,
            'city'   => $user->city,
            'zip'   => $user->zip,
        ];

        return $this->successResponse($response, 'Profile fetched successfully.', 200);
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->errorResponse([], 'Unauthorized', 403);
        }

        if ($user->role == 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // 'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'username' => 'nullable|string|max:100|unique:users,username,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
        }
        // Image upload
        if ($request->file('avatar')) {
            MiaHelper::deleteFile($user->avatar); // Delete old image if exists
            $user->avatar = MiaHelper::uploadImageResize($request->file('avatar'), 'user-avatars', 150, 150);
        }

        $user->name = $request->name ?? $user->name;
        // $user->email = $request->email ?? $user->email;
        $user->phone = $request->phone ?? $user->phone;
        $user->username = $request->username ?? $user->username;
        $user->save();

        $response = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'username' => $user->username,
            'avatar' => $user->avatar ? asset($user->avatar) : asset('user.jpg'),
            'role'   => $user->role,
        ];

        return $this->successResponse($response, 'Profile updated successfully.', 200);
    }

    public function changePassword(Request $request)
    {
        // for sanctum Route::middleware('auth:sanctum') and $user = $request->user()
        // $user = Auth::user(); $user = auth()->user(); // works but not best practice

        $user = Auth::guard('api')->user(); //best practice

        // if (!$user) return $this->error([], 'Unauthenticated', 401); // optional

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), $validator->errors()->first(), 400);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return $this->errorResponse([], 'Old password is incorrect', 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->successResponse([], 'Password updated successfully', 200);
    }

    public function changeAddress(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->errorResponse([], 'Unauthorized', 401);
        }

        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
        }

        $user->address = $request->address;
        $user->city = $request->city;
        $user->zip = $request->zip;
        $user->save();

        $response = [
            'address' => $user->address,
            'city' => $user->city,
            'zip' => $user->zip,
        ];

        return $this->successResponse($response, 'Address updated successfully.', 200);
    }

    public function profileDelete(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->errorResponse([], 'Unauthorized', 401);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'nullable|string',
            'email'    => 'required|email',
            'password' => 'required|string',
            'reason'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
        }

        if ($user->email !== $request->email) {
            return $this->errorResponse([], 'Email not matched', 400);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse([], 'Password not matched', 400);
        }

        // Optional: Save delete reason if needed
        // DeleteReason::create([
        //     'user_id' => $user->id,
        //     'reason'  => $request->reason,
        // ]);

        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            // Token invalidation failed (optional logging)
        }

        $user->delete();

        return $this->successResponse([], 'Profile deleted and logged out successfully', 200);
    }

    // This method need for app publications
    public function appAccountDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'nullable|string',
            'email'    => 'required|email',
            'password' => 'required|string',
            'reason'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse([], 'Email or Password is incorrect', 400);
        }

        // Delete user account
        // $user->delete();
        // Hard delete the user
        $user->forceDelete();

        return $this->successResponse([], 'Profile deleted successfully', 200);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'user_latitude' => 'required|numeric|between:-90,90',
            'user_longitude' => 'required|numeric|between:-180,180',
        ]);

        $user = Auth::guard('api')->user();

        $user->user_latitude = $request->user_latitude;
        $user->user_longitude = $request->user_longitude;
        $user->save();

        $response = [
            'user_latitude' => $user->user_latitude,
            'user_longitude' => $user->user_longitude,
        ];

        return $this->successResponse($response, 'Location updated successfully', 200);
    }

    public function toggleLanguage(Request $request)
    {
        $user = auth()->user();

        // Toggle logic
        $newLang = $user->language === 'en' ? 'it' : 'en';

        $user->update([
            'language' => $newLang
        ]);

        $response = [
            'language' => $newLang,
        ];

        return $this->successResponse($response, 'Language switched successfully', 200);
    }
}
