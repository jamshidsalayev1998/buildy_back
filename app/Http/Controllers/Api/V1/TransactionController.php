<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Transaction\StoreTransactionRequest;
use App\Http\Requests\Api\V1\Transaction\UpdateTransactionRequest;
use App\Http\Resources\Api\V1\TransactionCollection;
use App\Http\Resources\Api\V1\TransactionResource;
use App\Models\Transaction;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Access\AuthorizationException;

class TransactionController extends Controller
{
    use ApiResponse;

    /**
     * Get all transactions with pagination
     */
    public function index(): JsonResponse
    {
        try {
            $transactions = Transaction::with(['user', 'transactionCategory'])
                ->visibleToUser()
                ->latest()
                ->paginate(request('per_page', 15));

            Log::info('Transactions list retrieved', [
                'user_id' => auth()->id(),
                'total_count' => $transactions->total()
            ]);

            return response()->json([
                'success' => true,
                'data' => new TransactionCollection($transactions),
                'message' => 'Kirim-chiqimlar ro\'yxati'
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to get transactions list', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kirim-chiqimlar ro\'yxatini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Get single transaction by ID
     */
    public function show(Transaction $transaction): JsonResponse
    {
        try {
            $this->authorize('view', $transaction);

            $transaction->load(['user', 'transactionCategory']);

            Log::info('Transaction details retrieved', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id
            ]);

            return response()->json([
                'success' => true,
                'data' => new TransactionResource($transaction),
                'message' => 'Kirim-chiqim ma\'lumotlari'
            ]);
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized transaction view attempt', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id
            ]);

            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to get transaction details', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kirim-chiqim ma\'lumotlarini olishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Create new transaction
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', Transaction::class);

            DB::beginTransaction();

            $data = $request->validated();
            $data['user_id'] = auth()->id();

            if ($request->hasFile('receipt_image')) {
                $data['receipt_image_path'] = $request->file('receipt_image')
                    ->store('receipts', 'public');
            }

            $transaction = Transaction::create($data);

            DB::commit();

            Log::info('Transaction created', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'type' => $transaction->type
            ]);

            return response()->json([
                'success' => true,
                'data' => new TransactionResource($transaction->load(['user', 'transactionCategory'])),
                'message' => 'Kirim-chiqim muvaffaqiyatli yaratildi'
            ], 201);
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized transaction creation attempt', [
                'user_id' => auth()->id()
            ]);

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create transaction', [
                'user_id' => auth()->id(),
                'data' => $request->except('receipt_image'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kirim-chiqim yaratishda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Update transaction
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        try {
            $this->authorize('update', $transaction);

            DB::beginTransaction();

            $data = $request->validated();

            if ($request->hasFile('receipt_image')) {
                if ($transaction->receipt_image_path) {
                    Storage::disk('public')->delete($transaction->receipt_image_path);
                }
                $data['receipt_image_path'] = $request->file('receipt_image')
                    ->store('receipts', 'public');
            }

            $transaction->update($data);

            DB::commit();

            Log::info('Transaction updated', [
                'editor_id' => auth()->id(),
                'transaction_id' => $transaction->id,
                'updated_fields' => array_keys($request->except('receipt_image'))
            ]);

            return response()->json([
                'success' => true,
                'data' => new TransactionResource($transaction->load(['user', 'transactionCategory'])),
                'message' => 'Kirim-chiqim ma\'lumotlari yangilandi'
            ]);
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized transaction update attempt', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id
            ]);

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update transaction', [
                'editor_id' => auth()->id(),
                'transaction_id' => $transaction->id,
                'data' => $request->except('receipt_image'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kirim-chiqim ma\'lumotlarini yangilashda xatolik yuz berdi',
                500
            );
        }
    }

    /**
     * Delete transaction
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        try {
            $this->authorize('delete', $transaction);

            DB::beginTransaction();

            if ($transaction->receipt_image_path) {
                Storage::disk('public')->delete($transaction->receipt_image_path);
            }

            $transaction->delete();

            DB::commit();

            Log::info('Transaction deleted', [
                'deleter_id' => auth()->id(),
                'transaction_id' => $transaction->id
            ]);

            return $this->successResponse(
                message: 'Kirim-chiqim o\'chirildi'
            );
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized transaction deletion attempt', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id
            ]);

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete transaction', [
                'deleter_id' => auth()->id(),
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Kirim-chiqimni o\'chirishda xatolik yuz berdi',
                500
            );
        }
    }
}
