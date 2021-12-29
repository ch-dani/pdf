<?php

namespace App\Http\Controllers\Admin;

use App\Contact;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ContactController extends Controller
{
    public function contact_form(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'message' => 'required|string|min:30'
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()[0]
            ]);

        $Contact = new Contact;
        $Contact->email = $request->input('email');
        $Contact->message = $request->input('message');
        $Contact->problem = is_null($request->input('allowAccessLastTask')) ? 0 : 1;
        $Contact->save();

        $data = array(
            "text" => $request->input('message'),
            "email" => $request->input('email'),
            "site_url" => \URL::to('/')
        );

        \Mail::send('emails.contact', $data, function ($message) {
            $domain = $_SERVER['SERVER_NAME'];
            $message->from("no-reply@$domain", 'DeftPDF')->subject("Contact form");
            $message->to(\App\Option::option('contact_email'));
        });

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function index()
    {
        return view('admin.contacts_form', [
            'Contacts' => Contact::get(),
            'js' => [
                asset('js/admin/contacts_form.js')
            ]
        ]);
    }

    public function show($id)
    {
        $Contact = Contact::find($id);

        if (is_null($Contact))
            return redirect(route('admin-contacts'));

        if (!$Contact->read) {
            $Contact->read = 1;
            $Contact->save();
        }

        return view('admin.contact-show', [
            'Contact' => $Contact,
            'js' => [
                asset('js/admin/contacts_form.js')
            ]
        ]);
    }

    public function delete(Request $request)
    {
        $Contact = Contact::find($request->input('contact_id'));

        if (is_null($Contact))
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found.'
            ]);

        $Contact->delete();

        return response()->json(['status' => 'success']);
    }
}