<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TransactionCategory;
use App\Http\Resources\Api\V1\TransactionCategoryResource;
use App\Http\Resources\Api\V1\TransactionCategoryCollection;
use App\Http\Requests\Api\V1\TransactionCategoryRequest;
use App\Http\Requests\Api\V1\TransactionCategory\UpdateTransactionCategoryRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class TransactionCategoryController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        // $this->authorizeResource(TransactionCategory::class);
    }

    /**
     * Get all transaction categories
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $categories = TransactionCategory::visibleToUser()
                ->latest()
                ->paginate(request('per_page', 10));

            Log::info('Transaction categories list retrieved', [
                'user_id' => auth()->id(),
                'total_count' => $categories->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => new TransactionCategoryCollection($categories),
                'message' => 'Kategoriyalar ro\'yxati'
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to get transaction categories list', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kategoriyalar ro\'yxatini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Create new transaction category
     */
    public function store(TransactionCategoryRequest $request): JsonResponse
    {

        /** @var \App\Models\User */
        $authUser = auth()->user();
        try {
            DB::beginTransaction();

            $companyId = null;

            if ($authUser->hasRole('admin')) {
                $companyId = $authUser->admin->company_id;
            }

            if ($authUser->hasRole('manager')) {
                $companyId = $authUser->manager->company_id;
            }

            if ($authUser->hasRole('planner')) {
                $companyId = $authUser->planner->company_id;
            }

            $category = TransactionCategory::create([
                'name' => $request->name,
                'type' => $request->type,
                'company_id' => $companyId
            ]);

            DB::commit();

            Log::info('Transaction category created', [
                'creator_id' => auth()->id(),
                'category_id' => $category->id,
                'company_id' => $category->company_id
            ]);

            return response()->json([
                'success' => true,
                'data' => new TransactionCategoryResource($category),
                'message' => 'Kategoriya muvaffaqiyatli yaratildi'
            ], 201);
        } catch (AuthorizationException $e) {
            DB::rollBack();
            Log::warning('Unauthorized transaction category creation attempt', [
                'user_id' => auth()->id()
            ]);

            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to create transaction category', [
                'creator_id' => auth()->id(),
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kategoriya yaratishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Get single transaction category
     */
    public function show(TransactionCategory $transactionCategory): JsonResponse
    {
        try {
            $this->authorize('view', $transactionCategory);

            Log::info('Transaction category details retrieved', [
                'user_id' => auth()->id(),
                'category_id' => $transactionCategory->id
            ]);

            return response()->json([
                'success' => true,
                'data' => new TransactionCategoryResource($transactionCategory),
                'message' => 'Kategoriya ma\'lumotlari'
            ]);
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized transaction category view attempt', [
                'user_id' => auth()->id(),
                'category_id' => $transactionCategory->id
            ]);

            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to get transaction category details', [
                'user_id' => auth()->id(),
                'category_id' => $transactionCategory->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kategoriya ma\'lumotlarini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Update transaction category
     */
    public function update(UpdateTransactionCategoryRequest $request, TransactionCategory $transactionCategory)
    {
        try {
            $this->authorize('update', $transactionCategory);

            DB::beginTransaction();

            $transactionCategory->update([
                'name' => $request->name,
                'type' => $request->type
            ]);

            DB::commit();

            Log::info('Transaction category updated', [
                'editor_id' => auth()->id(),
                'category_id' => $transactionCategory->id,
                'updated_fields' => ['name', 'type']
            ]);

            return response()->json([
                'success' => true,
                'data' => new TransactionCategoryResource($transactionCategory),
                'message' => 'Kategoriya ma\'lumotlari yangilandi'
            ]);
        } catch (AuthorizationException $e) {
            DB::rollBack();
            Log::warning('Unauthorized transaction category update attempt', [
                'user_id' => auth()->id(),
                'category_id' => $transactionCategory->id
            ]);

            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to update transaction category', [
                'editor_id' => auth()->id(),
                'category_id' => $transactionCategory->id,
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kategoriya ma\'lumotlarini yangilashda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Delete transaction category
     */
    public function destroy(TransactionCategory $transactionCategory): JsonResponse
    {
        try {
            $this->authorize('delete', $transactionCategory);

            DB::beginTransaction();

            $transactionCategory->delete();

            DB::commit();

            Log::info('Transaction category deleted', [
                'deleter_id' => auth()->id(),
                'category_id' => $transactionCategory->id
            ]);

            return $this->successResponse(
                message: 'Kategoriya o\'chirildi'
            );
        } catch (AuthorizationException $e) {
            DB::rollBack();
            Log::warning('Unauthorized transaction category deletion attempt', [
                'user_id' => auth()->id(),
                'category_id' => $transactionCategory->id
            ]);

            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to delete transaction category', [
                'deleter_id' => auth()->id(),
                'category_id' => $transactionCategory->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kategoriyani o\'chirishda xatolik yuz berdi',
                500
            );
        }
    }
}
