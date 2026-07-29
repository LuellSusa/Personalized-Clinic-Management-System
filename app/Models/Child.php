<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Child extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_profile_id',
        'patient_number',
        'first_name',
        'middle_name',
        'last_name',
        'birth_date',
        'sex',
        'blood_type',
        'allergies',
        'medical_conditions',
        'current_medications',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function parentProfile(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
