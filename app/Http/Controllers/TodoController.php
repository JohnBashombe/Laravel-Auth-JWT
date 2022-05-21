<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery\Undefined;

class TodoController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api');
    }

    public function index()
    {
        $todos = Todo::all();
        return response()->json([
            'status' => 'success',
            'data' => $todos
        ], 200);
    }

    public function store(Request $request)
    {
        $rules = array(
            'title' => 'required|string|max:255',
            'description' => 'required|string'
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

        $todo = Todo::create([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Todo Created',
            'data' => $todo
        ], 201);
    }


    public function show($id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json([
                'status' => 'error',
                'message' => 'not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $todo
        ], 200);
    }

    public function update(Request $request, $id)
    {

        $rules = array(
            'title' => 'required|string|max:255',
            'description' => 'required|string'
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

        $todo = Todo::find($id);
        $todo->title = $request->title;
        $todo->description = $request->description;
        $todo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'updated',
            'data' => $todo
        ], 200);
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json([
                'status' => 'error',
                'message' => 'error',
            ], 400);
        }

        $todo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'delete',
            'data' => $todo,
        ], 200);
    }
}
