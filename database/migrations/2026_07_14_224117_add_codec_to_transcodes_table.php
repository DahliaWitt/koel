<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transcodes', static function (Blueprint $table): void {
            $table->string('codec', 16)->default('aac')->after('bit_rate');
            $table->dropUnique(['song_id', 'bit_rate']);
            $table->unique(['song_id', 'bit_rate', 'codec']);
        });
    }

    public function down(): void
    {
        DB::table('transcodes')->where('codec', '!=', 'aac')->delete();

        Schema::table('transcodes', static function (Blueprint $table): void {
            $table->dropUnique(['song_id', 'bit_rate', 'codec']);
            $table->dropColumn('codec');
            $table->unique(['song_id', 'bit_rate']);
        });
    }
};
