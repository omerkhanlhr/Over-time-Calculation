<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;

class ClientController extends Controller
{

    public function Addclient()
    {
        return view('clients.add_client');
    }

    public function Saveclient(Request $req)
    {
        $req->validate([
            'name' => 'required',
        ]);

        $existingEmail = Client::where('email', $req['email'])->first();

        if ($existingEmail) {
            $notification = array(
                'message' => 'Email already exists.',
                'alert-type' => 'error',
            );

            return redirect()->back()->with($notification);
        }


        $client = new Client();
        $client->name = $req['name'];
        $client->email = $req['email'];
        $client->company = $req['company'];
        $client->address = $req['address'];
        $client->client_id = 'CT-';
        $client->save();
        $client->update([
            'client_id'=>'CT-'.$client->id,
        ]);
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
        $client->email=$req['email'];
        $client->company=$req['company'];
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
    $client = Client::with('workHours')->findOrFail($id);
    return view('clients.single_client', compact('client'));
}


    public function Deleteclient($id)
    {
        $client=Client::findOrFail($id);

        if($client)
        {
            $client->delete();
        $notification=array(
            'message'=>'Client Deleted Successfully',
            'alert-type'=>'success'
        );
        return redirect()->back()->with($notification);
        }

        else
        {
            $notification=array(
                'message'=>'Something Went Wrong',
                'alert-type'=>'error'
            );
            return redirect()->back()->with($notification);
        }


    }


}
