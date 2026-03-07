<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpSendMail;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    use ApiResponse;
    const MAX_ATTEMPTS = 3;
    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'username'  => 'nullable|string|max:255|unique:users,username',
            'email'     => 'required|email|max:255|unique:users,email',
            'phone'     => 'nullable|string|max:20|unique:users,phone',
            'password'  => 'required|string|min:6|confirmed',
            'role'      => 'nullable|string', // optional role
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Password and confirmation password do not match',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
        }

        // if ($validator->fails()) {
        //     return $this->error($validator->errors(), $validator->errors()->first(), 422);
        // }

        DB::beginTransaction();

        try {
            // Create new user instance
            $user = new User();
            $user->name     = $request->name;
            $user->username = $request->username;
            $user->email    = $request->email;
            $user->phone    = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            DB::commit();

            // Auto login (generate JWT token)
            $token = JWTAuth::fromUser($user);

            // Response Data
            $userData = [
                'id'            => $user->id,
                'name'          => $user->name,
                'username'      => $user->username ?? '',
                'email'         => $user->email,
                'phone'         => $user->phone ?? '',
                'avatar'        => asset($user->avatar ?? 'user.jpg'),
                'token'         => $token,
                'role'          => $user->role ?? null,
            ];

            return $this->successResponse($userData, 'Registration successful', 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->errorResponse([], 'Registration failed: ' . $e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
        }

        // Attempt login
        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return $this->errorResponse([], 'Invalid email or password', 401);
        }

        $user = auth()->user();

        // Role-based restriction
        // if (!$user->hasRole('user')) {
        //     return $this->error([], 'Access denied. Only user accounts can login via API.', 403);
        // }

        // Only allow role = 'user' (User)
        if ($user->role !== 'user') {
            return $this->errorResponse([], 'Access denied. Only user accounts can login via API.', 403);
        }

        // Check account status
        if ($user->status == 0) {
            return $this->errorResponse([], 'Your account is inactive. Please contact admin.', 403);
        }


        // Update last login timestamp
        $user->last_login_at = now();
        $user->save();

        // Prepare user data for frontend
        $userData = [
            'id'        => $user->id,
            'name'      => $user->name ?? '',
            'username'  => $user->username ?? '',
            'email'     => $user->email,
            'phone'     => $user->phone ?? '',
            'avatar'    => asset($user->avatar ?? 'user.jpg'),
            'token'     => $token,
            'role'      => $user->role ?? null,
        ];

        // Return success response
        return $this->successResponse($userData, 'Login successful', 200);
    }

    public function logout(Request $request)
    {
        try {
            // Get token from request header
            $token = JWTAuth::getToken();

            if (!$token) {
                return $this->errorResponse([], 'Token not provided', 401);
            }

            $user = JWTAuth::authenticate($token);

            // Invalidate token
            JWTAuth::invalidate($token);

            return $this->successResponse(['name' => $user?->name,], 'Successfully logged out', 200);
        } catch (TokenInvalidException $e) {
            return $this->errorResponse([], 'Token is invalid', 401);
        } catch (JWTException $e) {
            return $this->errorResponse([], 'Logout failed: ' . $e->getMessage(), 500);
        }
    }


    //---- Forget password steps Customer start----------
    //------------------------------------------

    /**
     * Step 1: Send OTP to email or phone
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email'        => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse([], 'User with this email does not exist.', 404);
        }

        $otp = rand(100000, 999999); // 6 digit OTP
        $expiredAt = Carbon::now()->addMinutes(10); // OTP expires in 10 minutes

        $user->update([
            'otp'       => $otp,
            'otp_verified'  => false,
            'otp_attempts'  => 0,
            'otp_expired_at' => $expiredAt,
        ]);

        // Send OTP via email
        Mail::to($user->email)->send(new OtpSendMail($otp));

        return $this->successResponse([
            'email' => $user->email,
            'otp' => $otp, // for testing
            'otp_verified' => $user->otp_verified,
            'otp_attempts' => $user->otp_attempts,
            'otp_expired_at' => $user->otp_expired_at,

        ], 'OTP sent successfully.', 200);
    }

    /**
     * Step 2: Verify OTP (no email/phone needed from frontend)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();

        if (!$user) {
            return $this->errorResponse([], 'Invalid otp', 400);
        } else if ($user->otp_expired_at < Carbon::now()) {
            $user->otp = null;
            $user->otp_expired_at = null;
            $user->save();
            return $this->errorResponse([], 'OTP expired', 400);
        }

        $user->otp = null;
        $user->otp_expired_at = null;
        $user->otp_verified = true;
        $user->otp_verified_at                 = Carbon::now();
        $user->password_reset_token            = Str::random(64);
        $user->password_reset_token_expired_at = Carbon::now()->addMinutes(10); // 10 minutes
        $user->save();

        return $this->successResponse([
            'email' => $user->email,
            'password_reset_token' => $user->password_reset_token,
        ], 'OTP verified successfully', 200);
    }

    /**
     * Step 3: Reset password (only new password + confirmation)
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'       => 'required|email',
            'password'    => 'required|string|min:6|confirmed',
            'password_reset_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Error in Validation', 422);
        }

        $user = User::where('email', $request->email)->where('password_reset_token', $request->password_reset_token)->first();

        if (!$user) {
            return $this->errorResponse([], 'Invalid token or email.', 400);
        }

        if ($user->password_reset_token_expired_at < Carbon::now()) {
            return $this->errorResponse([], 'Token expired.', 400);
        }

        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->password_reset_token_expired_at = null;
        $user->save();


        // Attempt login after saving new password
        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return $this->errorResponse([], 'Unable to login. Please try again.', 401);
        }

        $userData = [
            'id'       => $user->id,
            'token'    => $token,
            'name'     => $user->name ?? 'User_name_' . uniqid(),
            'email'    => $user->email,
            'username' => $user->username ?? 'Username_' . uniqid(),
            'avatar'   => asset($user->avatar ?? 'user.jpg'),
        ];

        return $this->successResponse($userData, 'Password reset & login successful', 200);
    }

    //---- Forget password steps Customer end----------
    //------------------------------------------

    // ------ FCM token start -----------------
    //-----------------------------------
    public function storeFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'fcm_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Error in Validation', 422);
        }

        $user = Auth::guard('api')->user();

        // Update user table directly
        $user->update([
            'device_id' => $request->device_id,
            'fcm_token' => $request->fcm_token,
        ]);

        $response = [
            'device_id' => $user->device_id,
            'fcm_token' => $user->fcm_token,
        ];

        return $this->successResponse($response, 'FCM token stored successfully', 200);
    }


    public function deleteFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Error in Validation', 422);
        }

        $user = Auth::guard('api')->user();

        if ($user->device_id === $request->device_id) {
            $user->update([
                'device_id' => null,
                'fcm_token' => null,
            ]);
        }

        return $this->successResponse([], 'FCM token deleted successfully', 200);
    }

    // ------ FCM token end -----------------
    //--------------------------------------
}
