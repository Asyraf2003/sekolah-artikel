<?php

namespace App\Models;

use App\Enums\PpdbStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpdbApplication extends Model
{
    // hanya field input publik yang boleh mass-assign
    protected $fillable = [
        'full_name',
        'email',
        'whatsapp',
        'payment_proof_path',
        'public_code'
    ];

    protected $casts = [
        'status' => PpdbStatus::class,
        'verified_at' => 'datetime',
    ];

    public function tokens(): HasMany
    {
        return $this->hasMany(PpdbToken::class, 'ppdb_application_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ===== State transitions (biar tidak ngawur) =====

    public function markApproved(User $admin): void
    {
        $this->forceFill([
            'status' => PpdbStatus::APPROVED,
            'rejected_reason' => null,
            'verified_at' => now(),
            'verified_by' => $admin->id,
        ])->save();
    }

    public function markRejected(User $admin, string $reason): void
    {
        $this->forceFill([
            'status' => PpdbStatus::REJECTED,
            'rejected_reason' => $reason,
            'verified_at' => now(),
            'verified_by' => $admin->id,
        ])->save();
    }

    public function markActivated(User $user): void
    {
        $this->forceFill([
            'user_id' => $user->id,
            'status' => PpdbStatus::ACTIVATED,
        ])->save();
    }

    // ketika user edit (baik via token rejected atau via akun setelah activated)
    public function markResubmitted(): void
    {
        $this->forceFill([
            'status' => PpdbStatus::SUBMITTED,
            'rejected_reason' => null,
            'verified_at' => null,
            'verified_by' => null,
        ])->save();
    }
}
