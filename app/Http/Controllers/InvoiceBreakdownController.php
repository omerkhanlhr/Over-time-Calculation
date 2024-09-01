<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceBreakdown;
use Illuminate\Http\Request;

class InvoiceBreakdownController extends Controller
{
    public function editBreakdown($invoiceId, $laborType)
{
    // Fetch the invoice with its breakdowns and associated labour data
    $invoice = Invoice::with(['invoiceBreakdowns.labour' => function ($query) use ($laborType) {
        $query->where('name', $laborType);
    }])->findOrFail($invoiceId);

    $breakdown = $invoice->invoiceBreakdowns()->whereHas('labour', function ($query) use ($laborType) {
        $query->where('name', $laborType);
    })->first();

    return view('invoices.breakdown_invoices.edit_invoice_breakdown', compact('invoice', 'breakdown', 'laborType'));
}
public function updateBreakdown(Request $request, $invoiceId, $laborType)
{
    $request->validate([
        'rate' => 'required|numeric|min:0',
    ]);

    // Fetch the invoice with its breakdowns and associated labour data
    $invoice = Invoice::with('invoiceBreakdowns.labour')->findOrFail($invoiceId);

    // Initialize total amount to zero before recalculating
    $totalAmount = 0;

    // Update the rate and recalculate the amounts for each breakdown
    $breakdowns = $invoice->invoiceBreakdowns()->whereHas('labour', function ($query) use ($laborType) {
        $query->where('name', $laborType);
    })->get();

    foreach ($breakdowns as $breakdown) {
        // Update the rate
        $breakdown->rate = $request->rate;

        // Recalculate the subtotal and total_amount based on the new rate
        $breakdown->subtotal = $breakdown->hours_worked * $request->rate;
        $breakdown->total_amount = $breakdown->subtotal + $breakdown->overtime_amount;

        // Save the updated breakdown
        $breakdown->save();

        // Add the updated total_amount to the invoice's total
        $totalAmount += $breakdown->total_amount;
    }

    // Update the invoice's total_amount and grand_total based on recalculations
    $invoice->total_amount = $totalAmount;
    $tax = $invoice->tax;
    $invoice->grand_total = $invoice->total_amount + ($invoice->total_amount * ($tax / 100));  // Assuming 'tax' is a fixed amount or percentage
    $invoice->save();

    $notification = [
        'message' => 'Invoice Updated Successfully',
        'alert-type' => 'success',
    ];

    return redirect()->route('invoice.pdfs', $invoiceId)->with($notification);
}









}
