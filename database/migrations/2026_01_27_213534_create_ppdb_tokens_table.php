<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ppdb_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ppdb_application_id')
                ->constrained('ppdb_applications')
                ->cascadeOnDelete();

            // activation | edit
            $table->string('type', 20);

            // simpan hash (sha256) biar token plaintext nggak nongkrong di DB
            $table->char('token_hash', 64)->unique();

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();

            // siapa yang generate (admin), opsional
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['ppdb_application_id', 'type']);
            $table->index(['type', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_tokens');
    }
};
