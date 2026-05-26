<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdInsuranceDocument extends Model
{
    protected $fillable = [
    'household_insurance_id',
    'document_type',
    'document_title',
    'uploaded_by_user_id',
    'original_name',
    'file_name',
    'file_path',
    'file_type',
    'file_size',
];

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(HouseholdInsurance::class, 'household_insurance_id');
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}