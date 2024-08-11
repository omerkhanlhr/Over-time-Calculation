<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Labour extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'labours';

    public function workhours()
    {
        return $this->hasMany(Workhour::class);
    }
}
