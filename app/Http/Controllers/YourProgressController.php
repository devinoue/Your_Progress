<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Todos;

class YourProgressController extends Controller
{
    public function index()
    {
        $todos = Todos::all();
        return $todos;
    }
    public function create()
    {

    }
    public function store(Request $request)
    {
        return ["store"=>"is here"];
    }
    public function show()
    {
        //
    }
    public function update(Request $request, $id_name)
    {
        $todo = Todos::find($id_name);
        $todo->progress = $request->p_rate;
        $todo->save();
        return ['message' => 'update is completed'];
    }
    public function destroy(Request $request, $id_name)
    {
        return ['update' => $id_name];
    }
    public function edit()
    {
        //
    }
}
