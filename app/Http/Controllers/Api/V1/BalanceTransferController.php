<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Admin;
use App\Http\Requests\Api\V1\BalanceTransfer\CompanyToEmployeeRequest;
use App\Http\Requests\Api\V1\BalanceTransfer\EmployeeToCompanyRequest;
use App\Http\Requests\Api\V1\BalanceTransfer\CompanyToAdminRequest;
use App\Http\Requests\Api\V1\BalanceTransfer\AdminToCompanyRequest;
use App\Models\BalanceHistory;
use Illuminate\Support\Facades\DB;

class BalanceTransferController extends Controller
{
    public function companyToEmployee(CompanyToEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();
             /** @var \App\Models\User */
            $user = auth()->user();
            if ($user->hasRole('admin')) {
                $company = Company::findOrFail($user->admin->company_id);
            } else {
                $company = Company::findOrFail($user->employee->company_id);
            }
            $employee = Employee::findOrFail($request->employee_id);

            if ($company->balance < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kompaniyada yetarli mablag\' mavjud emas'
                ], 400);
            }

            $company->balance -= $request->amount;
            $employee->balance += $request->amount;

            $company->save();
            $employee->save();

            BalanceHistory::create([
                'from_id' => $company->id,
                'from_type' => Company::class,
                'to_id' => $employee->id,
                'to_type' => Employee::class,
                'amount' => $request->amount,
                'description' => 'Company to Employee'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mablag\' muvaffaqiyatli o\'tkazildi',
                'data' => [
                    'company_balance' => $company->balance,
                    'employee_balance' => $employee->balance
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function employeeToCompany(EmployeeToCompanyRequest $request)
    {
        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($request->employee_id);
            $company = Company::findOrFail($request->company_id);

            if ($employee->balance < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xodimda yetarli mablag\' mavjud emas'
                ], 400);
            }

            $employee->balance -= $request->amount;
            $company->balance += $request->amount;

            $employee->save();
            $company->save();

            BalanceHistory::create([
                'from_id' => $employee->id,
                'from_type' => Employee::class,
                'to_id' => $company->id,
                'to_type' => Company::class,
                'amount' => $request->amount,
                'description' => 'Employee to Company'
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mablag\' muvaffaqiyatli o\'tkazildi',
                'data' => [
                    'employee_balance' => $employee->balance,
                    'company_balance' => $company->balance
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function companyToAdmin(CompanyToAdminRequest $request)
    {
        try {
            DB::beginTransaction();

            $company = Company::findOrFail($request->company_id);
            $admin = Admin::findOrFail($request->admin_id);

            if ($company->balance < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kompaniyada yetarli mablag\' mavjud emas'
                ], 400);
            }

            $company->balance -= $request->amount;
            $admin->balance += $request->amount;

            $company->save();
            $admin->save();

            BalanceHistory::create([
                'from_id' => $company->id,
                'from_type' => Company::class,
                'to_id' => $admin->id,
                'to_type' => Admin::class,
                'amount' => $request->amount,
                'description' => 'Company to Admin'
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mablag\' muvaffaqiyatli o\'tkazildi',
                'data' => [
                    'company_balance' => $company->balance,
                    'admin_balance' => $admin->balance
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminToCompany(AdminToCompanyRequest $request)
    {
        try {
            DB::beginTransaction();

            $admin = Admin::findOrFail($request->admin_id);
            $company = Company::findOrFail($request->company_id);

            if ($admin->balance < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adminda yetarli mablag\' mavjud emas'
                ], 400);
            }

            $admin->balance -= $request->amount;
            $company->balance += $request->amount;

            $admin->save();
            $company->save();

            BalanceHistory::create([
                'from_id' => $admin->id,
                'from_type' => Admin::class,
                'to_id' => $company->id,
                'to_type' => Company::class,
                'amount' => $request->amount,
                'description' => 'Admin to Company'
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mablag\' muvaffaqiyatli o\'tkazildi',
                'data' => [
                    'admin_balance' => $admin->balance,
                    'company_balance' => $company->balance
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }
}
