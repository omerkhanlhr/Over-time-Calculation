<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceBreakdown;
use Illuminate\Http\Request;

class InvoiceBreakdownController extends Controller
{
    public function deleteInvoiceBreakdown($id)
    {
        $breakdown = InvoiceBreakdown::findOrFail($id);

        $invoice = Invoice::findOrFail($breakdown->invoice_id);

        $invoice->total_amount -= $breakdown->total_amount;

        // Recalculate the grand total
        $invoice->grand_total = $invoice->total_amount  +  ($invoice->tax / 100);

        // Save the updated invoice
        $check = $invoice->save();

        if ($check) {
            $breakdown->delete();

            $notification = [
                'message' => 'Invoice Breakdown Deleted Successfully',
                'alert-type' => 'success',
            ];
            return redirect()->back()->with($notification);
        } else {
            $notification = [
                'message' => 'Something Went Wrong',
                'alert-type' => 'error',
            ];
            return redirect()->back()->with($notification);
        }
    }

    public function editInvoiceBreakdown($id)
    {
        $breakdown = InvoiceBreakdown::with('labour')->findOrFail($id);

        return view('invoices.breakdown_invoices.edit_invoice_breakdown', compact('breakdown'));
    }

    public function updateInvoiceBreakdownRate(Request $request, $id)
    {
        $request->validate([
            'rate' => 'required|numeric|min:0',
        ]);

        // Retrieve the invoice breakdown to be updated
        $breakdown = InvoiceBreakdown::findOrFail($id);

        // Get the associated invoice
        $invoice = Invoice::findOrFail($breakdown->invoice_id);

        $rate = $request->rate;

        $breakdown->rate = $rate;

        $hoursWorked = $breakdown->hours_worked;

        $overtimeAmount = $breakdown->overtime_amount;

        if ($hoursWorked > 8)
        {
            $overtime = $hoursWorked - 8;
            $subtotal = 8 * $rate;
            $overtimeAmount = 1.5 * $overtime * $rate;
            $totalBreakdownAmount = $subtotal + $overtimeAmount;
        }
        else
        {
            $subtotal = $hoursWorked * $rate;
            $totalBreakdownAmount = $subtotal;
        }

        // Update the breakdown's rate and recalculate the subtotal and total amount


        $breakdown->subtotal = $subtotal;
        $breakdown->overtime_amount = $overtimeAmount; // Assuming overtime amount is calculated differently
        $breakdown->total_amount = $subtotal + $overtimeAmount;

        $breakdown->save();

        $totalAmount = InvoiceBreakdown::where('invoice_id', $invoice->id)->sum('total_amount');

        // Update the invoice's total amount and grand total
        $invoice->total_amount = $totalAmount;

        $invoice->grand_total = $totalAmount + ($totalAmount * ($invoice->tax / 100));;

        // Save the updated invoice
        $invoice->save();

        $notification = [
            'message' => 'Rate Updated Successfully and Invoice Totals Recalculated',
            'alert-type' => 'success',
        ];

        return redirect()->route('invoices.show')->with($notification);
    }
}
