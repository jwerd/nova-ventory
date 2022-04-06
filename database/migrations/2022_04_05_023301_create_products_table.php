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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('purchased_price', 8, 2)->default(0.00);
            $table->double('list_price', 8, 2)->default(0.00);
            $table->unsignedTinyInteger('available')->default(true);
            $table->unsignedTinyInteger('sold')->default(false);
            $table->foreignIdFor(\App\Models\User::class);
            $table->foreignIdFor(\App\Models\Company::class);
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
};
