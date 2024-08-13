<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceBreakdown extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'invoice_breakdowns';

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function labour()
    {
        return $this->belongsTo(Labour::class, 'labor_type_id');
    }

    
}
