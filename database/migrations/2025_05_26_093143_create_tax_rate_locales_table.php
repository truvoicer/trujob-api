<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tax_rate_locales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_rate_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('localeable_id');
            $table->string('localeable_type');
            $table->timestamps();

            $table->index(['tax_rate_id', 'localeable_id', 'localeable_type'], 'tax_rate_localeable_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tax_rate_locales');
    }
};
