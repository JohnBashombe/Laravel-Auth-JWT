<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {

        $rules = array(
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), true);
            $message = 'error';
            if (isset($errors['email'])) {
                $message = $errors['email'][0];
            } else if (isset($errors['password'])) {
                $message = $errors['password'][0];
            }

            return response()->json(['status' => 400, 'message' => $message], 400);
        }

        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 200);
    }

    public function register(Request $request)
    {

        $rules = array(
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), true);
            $message = 'error';
            if (isset($errors['name'])) {
                $message = $errors['name'][0];
            } else if (isset($errors['email'])) {
                $message = $errors['email'][0];
            } else if (isset($errors['password'])) {
                $message = $errors['password'][0];
            }

            return response()->json(['status' => 400, 'message' => $message], 400);
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 200);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ], 200);
    }

    public function refresh()
    {

        $user = Auth::user();
        $token = Auth::refresh();

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 200);
    }

    /**
     * Sends sms to user using Twilio's programmable sms client
     * @param Request $requests
     */
    public function sendMessage(Request $request)
    {
        $rules = array(
            // 'phone' => 'required|string|regex:/(+)[0-9]{12}'
            'phone' => 'required|string|min:11|max:13'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            error_log($validator->errors());
            $errors = json_decode($validator->errors(), true);
            $message = 'error';
            if (isset($errors['phone'])) {
                $message = $errors['phone'][0];
            }
            return response()->json(['status' => 400, 'message' => $message], 400);
        }

        return response()->json(['status' => 400, 'message' => "error"], 400);

        $body = "we msenge, njo apa kwanza tena changamka (:";
        $phone = $request->phone;
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $my_phone_number = getenv("TWILIO_NUMBER");

        try {
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($phone, ['from' => $my_phone_number, 'body' => $body]);
            //logic
            return response()->json([
                'status' => 200,
                'message' => 'message sent',
            ], 200);
        } catch (\Exception $exception) {
            // error_log($exception->getMessage());
            if (str_contains($exception, '[HTTP 400] Unable to create record:')) {
                return response()->json([
                    'status' => 400,
                    'message' => 'message not sent',
                ], 400);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'unknown error',
                ], 400);
            }
        }
    }
}
