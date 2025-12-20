<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    // Nama tabel (opsional, tapi aman ditulis eksplisit)
    protected $table = 'doctors';

    // Field yang boleh diisi (mass assignment)
    protected $fillable = [
        'name',
        'specialization',
        'phone',
        'schedule',
        'description',
        'photo',
    ];
}
