<?php

use App\Models\Country;
use App\Models\Currency;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId( 'listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId( 'country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId( 'currency_id')->constrained('currencies')->onDelete('cascade');
            $table->string('type');
            $table->double('amount', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_prices');
    }
};
