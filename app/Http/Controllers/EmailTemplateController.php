<?php

namespace App\Http\Controllers;


use App\Models\EmailTemplate;
use Illuminate\Http\Request;


class EmailTemplateController extends Controller
{
    public function index()
    {

        $templates =
            EmailTemplate::latest()->get();


        return view(
            'templates.index',
            compact('templates')
        );
    }

    public function store(Request $request)
    {

        $request->validate([

            'name' => 'required',
            'subject' => 'required',
            'body' => 'required'

        ]);

        EmailTemplate::create(
            $request->all()
        );

        return back()
            ->with(
                'success',
                'Template Created'
            );
    }

    public function destroy($id)
    {

        EmailTemplate::findOrFail($id)
            ->delete();

        return back();
    }
}
