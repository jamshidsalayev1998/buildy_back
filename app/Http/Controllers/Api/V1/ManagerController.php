<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Manager\StoreManagerRequest;
use App\Http\Requests\Api\V1\Manager\UpdateManagerRequest;
use App\Http\Resources\ManagerCollection;
use App\Http\Resources\ManagerResource;
use App\Models\Manager;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Access\AuthorizationException;

class ManagerController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        // $this->authorizeResource(Manager::class, 'manager');
    }

    /**
     * Get all managers with pagination
     */
    public function index(): JsonResponse
    {
        try {
            $managers = Manager::visibleToUser()
                ->latest()
                ->paginate(request('per_page', 15));

            Log::info('Managers list retrieved', [
                'user_id' => auth()->id(),
                'total_count' => $managers->total()
            ]);

            return $this->successResponse(
                new ManagerCollection($managers),
                'Menejerlar ro\'yxati'
            );

        } catch (\Throwable $e) {
            Log::error('Failed to get managers list', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Menejerlar ro\'yxatini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Get single manager by ID
     */
    public function show(Manager $manager): JsonResponse
    {
        try {
            $this->authorize('view', $manager);

            $manager->load(['user', 'company']);

            Log::info('Manager details retrieved', [
                'user_id' => auth()->id(),
                'manager_id' => $manager->id
            ]);

            return $this->successResponse(
                new ManagerResource($manager),
                'Menejer ma\'lumotlari'
            );

        } catch (\Throwable $e) {
            Log::error('Failed to get manager details', [
                'user_id' => auth()->id(),
                'manager_id' => $manager->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Menejer ma\'lumotlarini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Create new manager
     */
    public function store(StoreManagerRequest $request): JsonResponse
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

            // Assign manager role
            $user->assignRole('manager');

            // Create manager
            $manager = Manager::create([
                'user_id' => $user->id,
                'company_id' => auth()->user()->admin->company_id,
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

            Log::info('Manager created', [
                'creator_id' => auth()->id(),
                'manager_id' => $manager->id,
                'company_id' => $manager->company_id,
                'phone' => $manager->user->phone
            ]);

            return $this->successResponse(
                new ManagerResource($manager->load(['user', 'company'])),
                'Menejer muvaffaqiyatli yaratildi',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create manager', [
                'creator_id' => auth()->id(),
                'data' => $request->except('password'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Menejer yaratishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Update manager
     */
    public function update(UpdateManagerRequest $request, Manager $manager): JsonResponse
    {
        try {
            $this->authorize('update', $manager);
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
                    if ($manager->user->image_path) {
                        Storage::disk('public')->delete($manager->user->image_path);
                    }
                    $userData['image_path'] = $request->file('image')->store('users', 'public');
                }

                $manager->user->update($userData);
            }

            // Update manager
            $manager->update($request->except(['phone', 'password', 'image']));

            DB::commit();

            Log::info('Manager updated', [
                'editor_id' => auth()->id(),
                'manager_id' => $manager->id,
                'updated_fields' => array_keys($request->except(['phone', 'password', 'image']))
            ]);

            return $this->successResponse(
                new ManagerResource($manager->load(['user', 'company'])),
                'Menejer ma\'lumotlari yangilandi'
            );

        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized manager update attempt', [
                'user_id' => auth()->id(),
                'manager_id' => $manager->id
            ]);

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update manager', [
                'editor_id' => auth()->id(),
                'manager_id' => $manager->id,
                'data' => $request->except('password'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Menejer ma\'lumotlarini yangilashda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Delete manager
     */
    public function destroy(Manager $manager): JsonResponse
    {
        $this->authorize('delete', $manager);
        try {

            DB::beginTransaction();

            // Delete user image if exists
            if ($manager->user->image_path) {
                Storage::disk('public')->delete($manager->user->image_path);
            }

            // Delete user (will cascade delete manager due to foreign key constraint)
            $manager->user->delete();

            DB::commit();

            Log::info('Manager deleted', [
                'deleter_id' => auth()->id(),
                'manager_id' => $manager->id
            ]);

            return $this->successResponse(
                message: 'Menejer o\'chirildi'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete manager', [
                'deleter_id' => auth()->id(),
                'manager_id' => $manager->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Menejerni o\'chirishda xatolik yuz berdi',
                500
            );
        }
    }
}
