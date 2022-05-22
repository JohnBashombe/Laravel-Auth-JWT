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
            'title' => 'Master Pesa',
            'body' => 'This is for testing email using in master pesa.'
        ];

        Mail::to('iberthold248com')->send(new DemoMail($mailData));

        return response()->json([
            'message' => 'success',
        ], 200);

    }
}