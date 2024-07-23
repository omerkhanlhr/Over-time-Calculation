<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function getClientAddress($clientId)
{
    $client = Client::find($clientId);

    if ($client) {
        return response()->json(['address' => $client->address]);
    } else {
        return response()->json(['error' => 'Client not found'], 404);
    }
}
    public function Addclient()
    {
        return view('clients.add_client');
    }

    public function Saveclient(Request $req)
    {
        $req->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
        ]);

        $existingEmail = Client::where('contact_email', $req['email'])->first();
        $existingPhone = Client::where('phone', $req['phone'])->first();

        if ($existingEmail) {
            $notification = array(
                'message' => 'Email already exists.',
                'alert-type' => 'error',
            );

            return redirect()->back()->with($notification);
        }

        if ($existingPhone) {
            $notification = array(
                'message' => 'Phone number already exists.',
                'alert-type' => 'error',
            );

            return redirect()->back()->with($notification);
        }

        $client = new Client();
        $client->name = $req['name'];
        $client->contact_email = $req['email'];
        $client->company = $req['company'];
        $client->phone = $req['phone'];
        $client->address = $req['address'];
        $client->save();

        $notification = array(
            'message' => 'Client Added Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.clients')->with($notification);
    }


    public function allclient()
    {
        $clients=Client::all();
        return view('clients.all_clients',compact('clients'));
    }

    public function Editclient($id)
    {
        $client=Client::findOrFail($id);
        return view('clients.edit_client',compact('client'));
    }

    public function Updateclient(Request $req, $id)
    {
        $client=Client::findOrFail($id);
        $client->name=$req['name'];
        $client->contact_email=$req['email'];
        $client->company=$req['company'];
        $client->phone=$req['phone'];
        $client->address=$req['address'];
        $client->save();
        $notification=array(
            'message'=>'Client Updated Successfully',
            'alert-type'=>'success'
        );
        return redirect()->route('all.clients')->with($notification);
    }

    public function Singleclient($id)
    {
        $client=Client::findOrFail($id);
        return view('clients.single_client',compact('client'));
    }

    public function Deleteclient($id)
    {
        $client=Client::findOrFail($id);
        $client->delete();
        $notification=array(
            'message'=>'Client Deleted Successfully',
            'alert-type'=>'success'
        );
        return redirect()->back()->with($notification);
    }

    public function Viewinvoice($id)
    {
        $invoices=Invoice::where('client_id',$id)->get();
        return view('clients.invoice',compact('invoices'));
    }
}
