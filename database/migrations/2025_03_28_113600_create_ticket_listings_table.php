<?php

use App\Enums\Listing\Type\Ticket\TicketProperty;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'created_by_user_id')->constrained('users');
            $table->string(TicketProperty::WEBSITE->value)->nullable();
            $table->string(TicketProperty::PHONE->value)->nullable();
            $table->string(TicketProperty::EMAIL->value)->nullable();
            $table->string(TicketProperty::OPENING_HOURS->value)->nullable();
            $table->string(TicketProperty::CLOSING_HOURS->value)->nullable();
            $table->string(TicketProperty::TYPE->value)->nullable();
            $table->string(TicketProperty::START_DATE->value)->nullable();
            $table->string(TicketProperty::END_DATE->value)->nullable();
            $table->string(TicketProperty::START_TIME->value)->nullable();
            $table->string(TicketProperty::END_TIME->value)->nullable();
            $table->string(TicketProperty::SEAT_NUMBER->value)->nullable();
            $table->string(TicketProperty::SEAT_ROW->value)->nullable();
            $table->string(TicketProperty::SEAT_SECTION->value)->nullable();
            $table->string(TicketProperty::SEAT->value)->nullable();
            $table->string(TicketProperty::SEAT_SIDE->value)->nullable();
            $table->string(TicketProperty::SEAT_LEVEL->value)->nullable();
            $table->string(TicketProperty::SEAT_GATE->value)->nullable();
            $table->string(TicketProperty::SEAT_AREA->value)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
