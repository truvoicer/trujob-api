<?php

namespace Tests\Unit\Repositories;

use App\Models\TransactionAmount;
use App\Repositories\TransactionAmountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionAmountRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var TransactionAmountRepository
     */
    private $transactionAmountRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->transactionAmountRepository = new TransactionAmountRepository();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->transactionAmountRepository);
    }

    
    public function it_can_get_the_model(): void
    {
        $model = $this->transactionAmountRepository->getModel();

        $this->assertInstanceOf(TransactionAmount::class, $model);
    }

    
    public function it_can_find_by_params(): void
    {
        // Arrange
        TransactionAmount::factory()->count(3)->create();

        // Act
        $result = $this->transactionAmountRepository->findByParams('id', 'asc', 2);

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(TransactionAmount::class, $result->first());
    }

    
    public function it_can_find_by_query(): void
    {
        // Arrange
        TransactionAmount::factory()->count(5)->create();

        // Act
        $result = $this->transactionAmountRepository->findByQuery('some_query'); // The actual query is not used in the tested method

        // Assert
        $this->assertCount(5, $result);
        $this->assertInstanceOf(TransactionAmount::class, $result->first());
    }
}
