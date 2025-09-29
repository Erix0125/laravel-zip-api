<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'county_id'
    ];

    public function county()
    {
        return $this->belongsTo(County::class, 'county_id', 'id');
    }
}
