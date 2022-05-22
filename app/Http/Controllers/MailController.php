<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\DemoMail;

class MailController extends Controller
{
    public function index()
    {
        $mailData = [
            'title' => 'Mail from Gola',
            'body' => 'This is for testing email using smtp.'
        ];

        Mail::to('golokasindi@outlook.com')->send(new DemoMail($mailData));

        return response()->json([
            'message' => 'success',
        ], 200);

    }
}