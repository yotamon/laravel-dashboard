<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;
use Validator;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::latest()->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|unique:users|max:191|email|string',
            'type' => 'required',
            'bio' => '',
            'password' => 'required|max:191|string'
        ]);

        return User::Create([
            'name' => $user['name'],
            'email' => $user['email'],
            'type' => $user['type'],
            'bio' => $user['bio'],
            'password' => Hash::make($user['password'])
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return auth('api')->user();
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        // if($request->photo) {
        //     $extension = explode('/', mime_content_type($request->photo))[1];
        //     $name = time().'.'.$extension;

        //     \Image::make($request->photo)->save(public_path('img/profile/').$name);
        // }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|max:191|email|string|unique:users,email,'.$user->id,
            'password' => 'sometimes|min:6'
            ]);
            
        $user->update($request->all());

        return ['message' => 'User has been updated'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return ['message' => 'User has been deleted'];
    }
}
