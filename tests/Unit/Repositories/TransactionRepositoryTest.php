<?php

namespace Tests\Unit\Repositories;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionRepository $transactionRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionRepository = new TransactionRepository();
    }

    public function testGetModelReturnsCorrectModel(): void
    {
        $model = $this->transactionRepository->getModel();
        $this->assertInstanceOf(Transaction::class, $model);
    }

    public function testFindByParamsReturnsCollection(): void
    {
        Transaction::factory()->count(3)->create();

        $result = $this->transactionRepository->findByParams('id', 'asc');

        $this->assertCount(3, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function testFindByParamsReturnsCollectionWithCountLimit(): void
    {
        Transaction::factory()->count(5)->create();

        $result = $this->transactionRepository->findByParams('id', 'asc', 2);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function testFindByQueryReturnsAllTransactions(): void
    {
        Transaction::factory()->count(2)->create();

        $result = $this->transactionRepository->findByQuery([]);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }
}