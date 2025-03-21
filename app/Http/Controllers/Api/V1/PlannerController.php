<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Planner\StorePlannerRequest;
use App\Http\Requests\Api\V1\Planner\UpdatePlannerRequest;
use App\Http\Resources\Api\V1\PlannerCollection;
use App\Http\Resources\Api\V1\PlannerResource;
use App\Models\Planner;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Access\AuthorizationException;

class PlannerController extends Controller
{
    use ApiResponse;

    /**
     * Get all planners with pagination
     */
    public function index(): JsonResponse
    {
        try {
            $planners = Planner::with(['user', 'company'])
                ->visibleToUser()
                ->latest()
                ->paginate(request('per_page', 15));

            Log::info('Planners list retrieved', [
                'user_id' => auth()->id(),
                'total_count' => $planners->total()
            ]);

            return response()->json([
                'success' => true,
                'data' => new PlannerCollection($planners),
                'message' => 'Plannerlar ro\'yxati'
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to get planners list', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Plannerlar ro\'yxatini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Get single planner by ID
     */
    public function show(Planner $planner): JsonResponse
    {
        try {
            $this->authorize('view', $planner);

            // User ma'lumotlarini yuklash
            $planner->load('user');

            Log::info('Planner details retrieved', [
                'user_id' => auth()->id(),
                'planner_id' => $planner->id
            ]);

            return response()->json([
                'success' => true,
                'data' => new PlannerResource($planner),
                'message' => 'Planner ma\'lumotlari'
            ]);
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized planner view attempt', [
                'user_id' => auth()->id(),
                'planner_id' => $planner->id
            ]);

            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to get planner details', [
                'user_id' => auth()->id(),
                'planner_id' => $planner->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Planner ma\'lumotlarini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Create new planner
     */
    public function store(StorePlannerRequest $request): JsonResponse
    {
        try {

            /** @var \App\Models\User */
            $authUser = auth()->user();
            $companyId = null;

            if ($authUser->hasRole('admin')) {
                $companyId = $authUser->admin->company_id;
            } elseif ($authUser->hasRole('manager')) {
                $companyId = $authUser->manager->company_id;
            }

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

            // Assign planner role
            $user->assignRole('planner');

            // Create planner
            $planner = Planner::create([
                'user_id' => $user->id,
                'company_id' => $companyId,
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

            Log::info('Planner created', [
                'creator_id' => auth()->id(),
                'planner_id' => $planner->id,
                'company_id' => $planner->company_id,
                'phone' => $planner->user->phone
            ]);

            return response()->json([
                'success' => true,
                'data' => new PlannerResource($planner->load('user')),
                'message' => 'Planner muvaffaqiyatli yaratildi'
            ], 201);
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized planner creation attempt', [
                'user_id' => auth()->id()
            ]);

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create planner', [
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
     * Update planner
     */
    public function update(UpdatePlannerRequest $request, Planner $planner): JsonResponse
    {
        try {
            $this->authorize('update', $planner);

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
                    if ($planner->user->image_path) {
                        Storage::disk('public')->delete($planner->user->image_path);
                    }
                    $userData['image_path'] = $request->file('image')->store('users', 'public');
                }

                $planner->user->update($userData);
            }

            // Update planner
            $planner->update($request->except(['phone', 'password', 'image']));

            DB::commit();

            Log::info('Planner updated', [
                'editor_id' => auth()->id(),
                'planner_id' => $planner->id,
                'updated_fields' => array_keys($request->except(['phone', 'password', 'image']))
            ]);

            return response()->json([
                'success' => true,
                'data' => new PlannerResource($planner->load('user')),
                'message' => 'Planner ma\'lumotlari yangilandi'
            ]);
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized planner update attempt', [
                'user_id' => auth()->id(),
                'planner_id' => $planner->id
            ]);

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update planner', [
                'editor_id' => auth()->id(),
                'planner_id' => $planner->id,
                'data' => $request->except('password'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Planner ma\'lumotlarini yangilashda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Delete planner
     */
    public function destroy(Planner $planner): JsonResponse
    {
        try {
            $this->authorize('delete', $planner);

            DB::beginTransaction();

            // Delete user image if exists
            if ($planner->user->image_path) {
                Storage::disk('public')->delete($planner->user->image_path);
            }

            // Delete user (will cascade delete planner due to foreign key constraint)
            $planner->user->delete();

            DB::commit();

            Log::info('Planner deleted', [
                'deleter_id' => auth()->id(),
                'planner_id' => $planner->id
            ]);

            return $this->successResponse(
                message: 'Planner o\'chirildi'
            );
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized planner deletion attempt', [
                'user_id' => auth()->id(),
                'planner_id' => $planner->id
            ]);

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete planner', [
                'deleter_id' => auth()->id(),
                'planner_id' => $planner->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Plannerni o\'chirishda xatolik yuz berdi',
                500
            );
        }
    }
}
