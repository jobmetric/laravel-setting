<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('setting.tables.setting'), function (Blueprint $table) {
            $table->id();

            $table->string('code')->index();
            $table->string('key')->index();
            $table->text('value')->nullable()->default(null);
            $table->boolean('is_json')->default(false)->comment('If the array was in the value field -> is_json = true');

            $table->timestamps();
        });

        cache()->forget('setting');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('setting.tables.setting'));
    }
};
