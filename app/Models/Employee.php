<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $table="employees";

    public function workHours()
    {
        return $this->hasMany(WorkHour::class);
    }


}
