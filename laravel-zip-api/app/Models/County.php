<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    protected $table = 'counties';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name'
    ];

    public function cities()
    {
        return $this->hasMany(City::class, 'county_id', 'id');
    }
}
