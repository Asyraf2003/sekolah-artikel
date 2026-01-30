<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ppdb_applications', function (Blueprint $table) {
            $table->id();

            $table->string('public_code', 32)->unique();

            $table->string('full_name', 120);
            $table->string('email', 190)->unique();
            $table->string('whatsapp', 30)->unique();

            // path di disk private (ppdb_private)
            $table->string('payment_proof_path', 255);

            // status cuma 4
            $table->enum('status', ['submitted', 'approved', 'rejected', 'activated'])
                ->default('submitted');

            $table->text('rejected_reason')->nullable();

            // admin yang terakhir memutuskan (approve/reject)
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            // user terbentuk saat aktivasi
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_applications');
    }
};
