<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Employee\StoreEmployeeRequest;
use App\Http\Requests\Api\V1\Employee\UpdateEmployeeRequest;
use App\Http\Resources\Api\V1\EmployeeCollection;
use App\Http\Resources\Api\V1\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Access\AuthorizationException;

class EmployeeController extends Controller
{
    use ApiResponse;

    /**
     * Get all employees with pagination
     */
    public function index(): JsonResponse
    {
        try {
            $employees = Employee::with(['user', 'company'])
                ->visibleToUser()
                ->latest()
                ->paginate(request('per_page', 15));

            Log::info('Employees list retrieved', [
                'user_id' => auth()->id(),
                'total_count' => $employees->total()
            ]);

            return response()->json([
                'success' => true,
                'data' => new EmployeeCollection($employees),
                'message' => 'Xodimlar ro\'yxati'
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to get employees list', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Xodimlar ro\'yxatini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Get single employee by ID
     */
    public function show(Employee $employee): JsonResponse
    {
        try {
            $this->authorize('view', $employee);

            return response()->json([
                'success' => true,
                'data' => new EmployeeResource($employee->load('user')),
                'message' => 'Xodim ma\'lumotlari'
            ]);
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized employee view attempt', [
                'user_id' => auth()->id(),
                'employee_id' => $employee->id
            ]);

            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to get employee details', [
                'user_id' => auth()->id(),
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Xodim ma\'lumotlarini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Create new employee
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        try {
            // $this->authorize('create', $request->position);
            /** @var \App\Models\User */
            $authUser = auth()->user();
            $companyId = null;

            if ($authUser->hasRole('admin')) {
                $companyId = $authUser->admin->company_id;
            } elseif ($authUser->hasRole('superadmin')) {
                $companyId = $request->company_id;
            }
            elseif ($authUser->hasRole('manager')) {
                $companyId = $authUser->employee->company_id;
            }
            elseif ($authUser->hasRole('planner')) {
                $companyId = $authUser->employee->company_id;
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

            // Assign role based on position
            $user->assignRole($request->position);

            // Create employee
            $employee = Employee::create([
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
                'notes' => $request->notes,
                'created_by' => $authUser->id
            ]);

            DB::commit();

            Log::info('Employee created', [
                'creator_id' => auth()->id(),
                'employee_id' => $employee->id,
                'company_id' => $employee->company_id,
                'phone' => $employee->user->phone
            ]);

            return response()->json([
                'success' => true,
                'data' => new EmployeeResource($employee->load('user')),
                'message' => 'Xodim muvaffaqiyatli yaratildi'
            ], 201);
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized employee creation attempt', [
                'user_id' => auth()->id()
            ]);

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create employee', [
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
     * Update employee
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            $this->authorize('update', $employee);

            DB::beginTransaction();

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($employee->user->image_path) {
                    Storage::disk('public')->delete($employee->user->image_path);
                }

                $path = $request->file('image')->store('users', 'public');
                $employee->user->update(['image_path' => $path]);
            }

            // Update employee
            $employee->update($request->except(['password', 'image']));

            DB::commit();

            Log::info('Employee updated', [
                'editor_id' => auth()->id(),
                'employee_id' => $employee->id,
                'company_id' => $employee->company_id
            ]);

            return response()->json([
                'success' => true,
                'data' => new EmployeeResource($employee->load('user')),
                'message' => 'Xodim ma\'lumotlari yangilandi'
            ]);
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized employee update attempt', [
                'user_id' => auth()->id(),
                'employee_id' => $employee->id
            ]);

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update employee', [
                'editor_id' => auth()->id(),
                'employee_id' => $employee->id,
                'data' => $request->except('password'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Xodim ma\'lumotlarini yangilashda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Delete employee
     */
    public function destroy(Employee $employee): JsonResponse
    {
        try {
            $this->authorize('delete', $employee);

            DB::beginTransaction();

            // Delete user image if exists
            if ($employee->user->image_path) {
                Storage::disk('public')->delete($employee->user->image_path);
            }

            // Soft delete both employee and user
            $employee->status = 'inactive';
            $employee->save();
            $employee->delete();
            $employee->user->delete();

            DB::commit();

            Log::info('Employee soft deleted', [
                'deleter_id' => auth()->id(),
                'employee_id' => $employee->id
            ]);

            return $this->successResponse(
                message: 'Xodim arxivlandi'
            );
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized employee deletion attempt', [
                'user_id' => auth()->id(),
                'employee_id' => $employee->id
            ]);

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to soft delete employee', [
                'deleter_id' => auth()->id(),
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Xodimni arxivlashda xatolik yuz berdi',
                500
            );
        }
    }
}
