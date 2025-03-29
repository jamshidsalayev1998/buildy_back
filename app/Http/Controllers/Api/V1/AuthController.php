<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\Api\V1\EmployeeResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Login user and create token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('phone', $request->phone)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse(
                    'Telefon raqam yoki parol noto\'g\'ri',
                    401
                );
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'phone' => $user->phone
            ]);

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer'
            ], 'Muvaffaqiyatli login qilindi');

        } catch (\Throwable $e) {
            Log::error('Login failed', [
                'phone' => $request->phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Login qilishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Logout user (Revoke the token)
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();

            Log::info('User logged out successfully', [
                'user_id' => $user->id,
                'phone' => $user->phone
            ]);

            return $this->successResponse(
                message: 'Muvaffaqiyatli chiqish qilindi'
            );

        } catch (\Throwable $e) {
            Log::error('Logout failed', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Chiqish qilishda xatolik yuz berdi',
                500
            );
        }
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $profile = null;
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        if($user->hasRole('superadmin')) {
            $profile = null;
        }
        elseif($user->hasRole('admin')) {
            $profile = new AdminResource($user->admin);
            $profile->load('company');
        } else{
            $profile = new EmployeeResource($user->employee);
            $profile->load('company');
        }
        try {
            return $this->successResponse([
                'user' => new UserResource($user),
                'profile' => $profile,
                'permissions' => $permissions
            ]);
        } catch (\Throwable $e) {
            Log::error('Me failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
