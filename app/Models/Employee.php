<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $guarded=[];

    protected $table="employees";

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function setCnicAttribute($value)
    {
        $this->attributes['cnic'] = substr($value, 0, 5) . '-' . substr($value, 5, 7) . '-' . substr($value, 12, 1);
    }
}
