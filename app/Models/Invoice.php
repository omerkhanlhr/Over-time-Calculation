<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function workhours()
    {
        return $this->hasMany(Workhour::class, 'client_id', 'client_id');
    }

    public function invoiceBreakdowns()
    {
        return $this->hasMany(InvoiceBreakdown::class);
    }
}
