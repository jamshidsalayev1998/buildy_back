<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Company\StoreCompanyRequest;
use App\Http\Requests\Api\V1\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyCollection;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        try {

            $companies = Company::withTrashed()
                ->with('admins', 'managers')
                ->latest()
                ->paginate(request('per_page', 15));

            Log::info('Companies list retrieved', [
                'user_id' => auth()->id(),
                'total_count' => $companies->total()
            ]);

            return response()->json([
                'success' => true,
                'data' => new CompanyCollection($companies),
                'message' => 'Kompaniyalar ro\'yxati'
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to get companies list', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kompaniyalar ro\'yxatini olishda xatolik yuz berdi',
                500
            );
        }
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $company = Company::create($request->validated());

            DB::commit();

            Log::info('Company created', [
                'creator_id' => auth()->id(),
                'company_id' => $company->id
            ]);

            return response()->json([
                'success' => true,
                'data' => new CompanyResource($company),
                'message' => 'Kompaniya muvaffaqiyatli yaratildi'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create company', [
                'creator_id' => auth()->id(),
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kompaniya yaratishda xatolik yuz berdi',
                500
            );
        }
    }

    public function show(Company $company): JsonResponse
    {
        try {

            $company->load('admins', 'managers');

            Log::info('Company details retrieved', [
                'user_id' => auth()->id(),
                'company_id' => $company->id
            ]);

            return response()->json([
                'success' => true,
                'data' => new CompanyResource($company),
                'message' => 'Kompaniya ma\'lumotlari'
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to get company details', [
                'user_id' => auth()->id(),
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kompaniya ma\'lumotlarini olishda xatolik yuz berdi',
                500
            );
        }
    }

    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        try {
            DB::beginTransaction();

            $company->update($request->validated());

            DB::commit();

            Log::info('Company updated', [
                'editor_id' => auth()->id(),
                'company_id' => $company->id,
                'updated_fields' => array_keys($request->validated())
            ]);

            return response()->json([
                'success' => true,
                'data' => new CompanyResource($company),
                'message' => 'Kompaniya ma\'lumotlari yangilandi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update company', [
                'editor_id' => auth()->id(),
                'company_id' => $company->id,
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kompaniya ma\'lumotlarini yangilashda xatolik yuz berdi',
                500
            );
        }
    }

    public function destroy(Company $company): JsonResponse
    {
        try {
            DB::beginTransaction();

            $company->delete();

            DB::commit();

            Log::info('Company deleted', [
                'deleter_id' => auth()->id(),
                'company_id' => $company->id
            ]);

            return $this->successResponse(
                message: 'Kompaniya o\'chirildi'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete company', [
                'deleter_id' => auth()->id(),
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kompaniyani o\'chirishda xatolik yuz berdi',
                500
            );
        }
    }

    public function restore(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $company = Company::withTrashed()->findOrFail($id);
            $company->restore();

            DB::commit();

            Log::info('Company restored', [
                'restorer_id' => auth()->id(),
                'company_id' => $company->id
            ]);

            return response()->json([
                'success' => true,
                'data' => new CompanyResource($company),
                'message' => 'Kompaniya qayta tiklandi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore company', [
                'restorer_id' => auth()->id(),
                'company_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kompaniyani qayta tiklashda xatolik yuz berdi',
                500
            );
        }
    }
}
