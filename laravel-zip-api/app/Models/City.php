<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'zip_code',
        'name',
        'county_id'
    ];

    public function county()
    {
        return $this->belongsTo(County::class, 'county_id', 'id');
    }
}
