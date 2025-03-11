<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreAdminRequest;
use App\Http\Requests\Api\V1\Admin\UpdateAdminRequest;
use App\Models\Admin;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AdminCollection;
use App\Http\Resources\AdminResource;

class AdminController extends Controller
{
    use ApiResponse;

    /**
     * Get all admins with pagination
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('view admins');

            $admins = Admin::with('user')
                ->latest()
                ->paginate(request('per_page', 15));

            Log::info('Admins list retrieved', [
                'user_id' => auth()->id(),
                'total_count' => $admins->total()
            ]);

            return response()->json([
                'success' => true,
                'data' => new AdminCollection($admins),
                'message' => 'Adminlar ro\'yxati'
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to get admins list', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Adminlar ro\'yxatini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Get single admin by ID
     */
    public function show(Admin $admin): JsonResponse
    {
        try {


            // User ma'lumotlarini yuklash
            $admin->load('user');

            Log::info('Admin details retrieved', [
                'user_id' => auth()->id(),
                'admin_id' => $admin->id
            ]);

            return response()->json([
                'success' => true,
                'data' => new AdminResource($admin),
                'message' => 'Admin ma\'lumotlari'
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to get admin details', [
                'user_id' => auth()->id(),
                'admin_id' => $admin->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Admin ma\'lumotlarini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Create new admin
     */
    public function store(StoreAdminRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'phone' => $request->phone,
                'password' => Hash::make($request->password)
            ]);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('users', 'public');
                $user->image_path = $path;
                $user->save();
            }

            // Create admin
            $admin = Admin::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'gender' => $request->gender,
                'passport_number' => $request->passport_number,
                'birth_date' => $request->birth_date,
                'position' => $request->position,
                'work_type' => $request->work_type,
                'hourly_rate' => $request->hourly_rate,
                'monthly_salary' => $request->monthly_salary,
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            DB::commit();

            Log::info('Admin created', [
                'creator_id' => auth()->id(),
                'admin_id' => $admin->id,
                'phone' => $admin->user->phone
            ]);

            return response()->json([
                'success' => true,
                'data' => new AdminResource($admin->load('user')),
                'message' => 'Admin muvaffaqiyatli yaratildi'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create admin', [
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
     * Update admin
     */
    public function update(UpdateAdminRequest $request, Admin $admin): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Update user
            if ($request->has('phone') || $request->has('password') || $request->hasFile('image')) {
                $userData = [];

                if ($request->has('phone')) {
                    $userData['phone'] = $request->phone;
                }

                if ($request->has('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                if ($request->hasFile('image')) {
                    if ($admin->user->image_path) {
                        Storage::disk('public')->delete($admin->user->image_path);
                    }
                    $userData['image_path'] = $request->file('image')->store('users', 'public');
                }

                $admin->user->update($userData);
            }

            // Update admin
            $admin->update($request->except(['phone', 'password', 'image']));

            DB::commit();

            Log::info('Admin updated', [
                'editor_id' => auth()->id(),
                'admin_id' => $admin->id,
                'updated_fields' => array_keys($request->except(['phone', 'password', 'image']))
            ]);

            return response()->json([
                'success' => true,
                'data' => new AdminResource($admin->load('user')),
                'message' => 'Admin ma\'lumotlari yangilandi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update admin', [
                'editor_id' => auth()->id(),
                'admin_id' => $admin->id,
                'data' => $request->except('password'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Admin ma\'lumotlarini yangilashda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Delete admin
     */
    public function destroy(Admin $admin): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Delete user image if exists
            if ($admin->user->image_path) {
                Storage::disk('public')->delete($admin->user->image_path);
            }

            // Delete user (will cascade delete admin due to foreign key constraint)
            $admin->user->delete();

            DB::commit();

            Log::info('Admin deleted', [
                'deleter_id' => auth()->id(),
                'admin_id' => $admin->id
            ]);

            return $this->successResponse(
                message: 'Admin o\'chirildi'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete admin', [
                'deleter_id' => auth()->id(),
                'admin_id' => $admin->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Adminni o\'chirishda xatolik yuz berdi',
                500
            );
        }
    }
}
