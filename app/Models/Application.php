<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mobile',
        'gender',
        'scan_copy',
        'email',
        'office_id',
        'service_id',
    ];

    public function scan_copy() : HasMany
    {
        return $this->hasMany(ScanCopy::class);
    }
}
