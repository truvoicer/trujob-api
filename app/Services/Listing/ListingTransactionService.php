<?php
namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\Transaction;
use App\Services\Transaction\TransactionService;

class ListingTransactionService extends TransactionService
{
    public function createListingTransaction(Listing $listing, array $data)
    {
        $data['user_id'] = $this->user->id;
        $transaction = $this->createTransaction($data);
        $listing->transactions()->attach($transaction->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return true;
    }

    public function updateListingTransaction(Listing $listing, Transaction $transaction, array $data)
    {
        $check = $listing->transactions()->where('transactions.id', $transaction->id)->first();
        if (!$check) {
            throw new \Exception('Transaction not found in listing');
        }
        
        $this->updateTransaction($transaction, $data);
        $listing->transactions()->updateExistingPivot($transaction->id, [
            'updated_at' => now(),
        ]);
        return true;
    }
    public function deleteListingTransaction(Listing $listing, Transaction $transaction)
    {
        $check = $listing->transactions()->where('transactions.id', $transaction->id)->first();
        if (!$check) {
            throw new \Exception('Transaction not found in listing');
        }
        $this->deleteTransaction($transaction);
        $listing->transactions()->detach($transaction->id);
        return true;
    }

}