<?php

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
        Schema::create('firebase_topic_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firebase_topic_id')->constrained('firebase_topics')->onDelete('cascade');
            $table->foreignId('firebase_device_id')->constrained('firebase_devices')->onDelete('cascade');
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
        Schema::dropIfExists('firebase_topic_devices');
    }
};
