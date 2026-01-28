<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpdbApplication extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'whatsapp',
        'payment_proof_path',
        'status',
        'rejected_reason',
        'verified_at',
        'verified_by',
        'user_id',
        'public_code',
    ];

    protected $casts = [
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
}
