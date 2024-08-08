<?php

namespace App\Http\Controllers;

use App\Models\Labour;
use Illuminate\Http\Request;

class LabourTypeController extends Controller
{
    public function add_type()
    {
        return view('labour_types.add_types');
    }

    public function all_type()
    {
        $labours = Labour::all();
        return view('labour_types.all_types', compact('labours'));
    }

    public function store_type(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:labours,name',
        ]);

        $labour = new Labour();
        $labour->name = $request['name'];
        $labour->save();

        $notification = array(
            'message' => 'Labour Type Added Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.type')->with($notification);
    }

    public function edit_type($id)
    {
        $labour = Labour::findOrFail($id);
        return view('labour_types.edit_types', compact('labour'));
    }

    public function update_type(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:labours,name',
        ]);

        $labour = Labour::findOrFail($id);
        $labour->name = $request['name'];
        $labour->save();

        $notification = array(
            'message' => 'Labour Type Updated Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.type')->with($notification);
    }


    public function delete_type($id)
    {
        $labour = Labour::findOrFail($id);
        if ($labour) {
            $labour->delete();
            $notification = array(
                'message' => 'Labour Type Deleted Successfully',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Something went wrong',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }
}
