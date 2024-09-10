<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailController extends Controller
{
    //
    public function index(){
        Mail::to('richardkoech69@gmail.com')->send(new TestMail(
            [
                'title' => 'The test mail',
                'body' => 'The Bosy',
            ]
        ));
    }
}
