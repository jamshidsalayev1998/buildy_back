<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\CreateAdminRequest;
use App\Http\Requests\Api\V1\User\CreateManagerRequest;
use App\Http\Requests\Api\V1\User\CreatePlannerRequest;
use App\Http\Requests\Api\V1\User\CreateEmployeeRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Create a new admin user (Only SuperAdmin)
     */
    public function createAdmin(CreateAdminRequest $request): JsonResponse
    {
        try {
            $this->authorize('create admins');

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('users', 'public');
                $data['image_path'] = $path;
            }

            $data['status'] = 'active';
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);
            $user->assignRole('admin');

            Log::info('Admin user created', [
                'creator_id' => auth()->id(),
                'user_id' => $user->id,
                'phone' => $user->phone
            ]);

            return $this->respondWithResource(
                new UserResource($user),
                'Admin muvaffaqiyatli yaratildi',
                201
            );

        } catch (\Throwable $e) {
            Log::error('Failed to create admin user', [
                'creator_id' => auth()->id(),
                'data' => $request->except('password'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Admin yaratishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Create a new manager user (Only SuperAdmin and Admin)
     */
    public function createManager(CreateManagerRequest $request): JsonResponse
    {
        try {
            $this->authorize('create managers');

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('users', 'public');
                $data['image_path'] = $path;
            }

            $data['status'] = 'active';
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);
            $user->assignRole('manager');

            Log::info('Manager user created', [
                'creator_id' => auth()->id(),
                'user_id' => $user->id,
                'phone' => $user->phone
            ]);

            return $this->respondWithResource(
                new UserResource($user),
                'Manager muvaffaqiyatli yaratildi',
                201
            );

        } catch (\Throwable $e) {
            Log::error('Failed to create manager user', [
                'creator_id' => auth()->id(),
                'data' => $request->except('password'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Manager yaratishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Create a new planner user (Only SuperAdmin, Admin and Manager)
     */
    public function createPlanner(CreatePlannerRequest $request): JsonResponse
    {
        try {
            $this->authorize('create planners');

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('users', 'public');
                $data['image_path'] = $path;
            }

            $data['status'] = 'active';
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);
            $user->assignRole('planner');

            Log::info('Planner user created', [
                'creator_id' => auth()->id(),
                'user_id' => $user->id,
                'phone' => $user->phone
            ]);

            return $this->respondWithResource(
                new UserResource($user),
                'Planner muvaffaqiyatli yaratildi',
                201
            );

        } catch (\Throwable $e) {
            Log::error('Failed to create planner user', [
                'creator_id' => auth()->id(),
                'data' => $request->except('password'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Planner yaratishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Create a new employee user (Only SuperAdmin, Admin, Manager and Planner)
     */
    public function createEmployee(CreateEmployeeRequest $request): JsonResponse
    {
        try {
            $this->authorize('create employees');

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('users', 'public');
                $data['image_path'] = $path;
            }

            $data['status'] = 'active';
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);
            $user->assignRole('employee');

            Log::info('Employee user created', [
                'creator_id' => auth()->id(),
                'user_id' => $user->id,
                'phone' => $user->phone
            ]);

            return $this->respondWithResource(
                new UserResource($user),
                'Xodim muvaffaqiyatli yaratildi',
                201
            );

        } catch (\Throwable $e) {
            Log::error('Failed to create employee user', [
                'creator_id' => auth()->id(),
                'data' => $request->except('password'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Xodim yaratishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): JsonResponse
    {
        try {
            $this->authorize('view ' . $role);

            $users = User::role($role)->get();

            Log::info('Users list retrieved', [
                'user_id' => auth()->id(),
                'role' => $role,
                'count' => $users->count()
            ]);

            return $this->respondWithResource(
                UserResource::collection($users),
                'Foydalanuvchilar ro\'yxati'
            );

        } catch (\Throwable $e) {
            Log::error('Failed to get users list', [
                'user_id' => auth()->id(),
                'role' => $role,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Foydalanuvchilar ro\'yxatini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Update user
     */
    public function updateUser(int $id, CreateEmployeeRequest $request): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $userRole = $user->roles->first()->name;

            $this->authorize('edit ' . $userRole . 's');

            $data = $request->validated();

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($user->image_path) {
                    Storage::disk('public')->delete($user->image_path);
                }

                $path = $request->file('image')->store('users', 'public');
                $data['image_path'] = $path;
            }

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            Log::info('User updated', [
                'editor_id' => auth()->id(),
                'user_id' => $user->id,
                'role' => $userRole,
                'updated_fields' => array_keys($data)
            ]);

            return $this->respondWithResource(
                new UserResource($user),
                'Foydalanuvchi ma\'lumotlari yangilandi'
            );

        } catch (\Throwable $e) {
            Log::error('Failed to update user', [
                'editor_id' => auth()->id(),
                'user_id' => $id,
                'data' => $request->except('password'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Foydalanuvchi ma\'lumotlarini yangilashda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $userRole = $user->roles->first()->name;

            $this->authorize('delete ' . $userRole . 's');

            // Delete user image if exists
            if ($user->image_path) {
                Storage::disk('public')->delete($user->image_path);
            }

            $user->delete();

            Log::info('User deleted', [
                'deleter_id' => auth()->id(),
                'user_id' => $id,
                'role' => $userRole
            ]);

            return $this->successResponse(
                message: 'Foydalanuvchi o\'chirildi'
            );

        } catch (\Throwable $e) {
            Log::error('Failed to delete user', [
                'deleter_id' => auth()->id(),
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Foydalanuvchini o\'chirishda xatolik yuz berdi',
                500
            );
        }
    }
}
