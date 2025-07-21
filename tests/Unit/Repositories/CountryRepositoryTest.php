<?php

namespace Tests\Unit\Repositories;

use App\Models\Country;
use App\Repositories\CountryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CountryRepository $countryRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->countryRepository = new CountryRepository();
    }

    public function testGetModelReturnsCountryModel(): void
    {
        $model = $this->countryRepository->getModel();
        $this->assertInstanceOf(Country::class, $model);
    }

    public function testFindByParamsReturnsCollectionOfCountries(): void
    {
        Country::factory()->count(3)->create();

        $countries = $this->countryRepository->findByParams('name', 'asc');

        $this->assertCount(3, $countries);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $countries);
        $this->assertInstanceOf(Country::class, $countries->first());
    }

    public function testFindByParamsReturnsCorrectNumberOfCountriesWhenCountIsProvided(): void
    {
        Country::factory()->count(5)->create();

        $countries = $this->countryRepository->findByParams('name', 'asc', 2);

        $this->assertCount(2, $countries);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $countries);
        $this->assertInstanceOf(Country::class, $countries->first());
    }

    public function testFindByQueryReturnsAllCountries(): void
    {
        Country::factory()->count(2)->create();

        $countries = $this->countryRepository->findByQuery('some query');

        $this->assertCount(2, $countries);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $countries);
        $this->assertInstanceOf(Country::class, $countries->first());
    }
}