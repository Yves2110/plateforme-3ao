<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FormationCertificate extends Model
{
    protected $fillable = [
        'enrollment_id',
        'user_id',
        'formation_id',
        'certificate_number',
        'learner_name',
        'learner_email',
        'learner_organization',
        'formation_title',
        'issued_at',
        'pdf_path',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(FormationEnrollment::class, 'enrollment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function getPdfFullPathAttribute(): ?string
    {
        if (! $this->pdf_path) {
            return null;
        }

        return Storage::disk('local')->path($this->pdf_path);
    }

    public function pdfExists(): bool
    {
        return $this->pdf_path && Storage::disk('local')->exists($this->pdf_path);
    }
}
