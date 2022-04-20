<?php

namespace App\Http\Controllers;

// import  User model,response and hash facades
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            "identifiant" => 'required|string|unique:users,identifiant|min:5',
            "email" => 'required|string|unique:users,email',
            "password" => 'required|string|confirmed|min:5',
            "sexe" => "required|string",
            "picture" => 'required|file',
            "birthday" => 'required|date'
        ]);
        if($request->hasFile('picture'))
        {   $filename = $request->email.'.'.$fields['picture']->getClientOriginalExtension();
            $fields['picture']->move(public_path('/uploads/images',$filename));
            $user = User::create([
                "identifiant" => $fields['identifiant'],
                "email" => $fields['email'],
                "password" => bcrypt($fields['password']),
                "sexe" => $fields["sexe"],
                "birthday" => $fields['birthday'],
                "picture" => $filename
            ]);
            $token = $user->createToken('user_token')->plainTextToken;
            $response = [
                'message' => 'User create succefuly',
                'user' => $user,
                'token' => $token,
                'succes' => true
            ];

            return response($response,201);
        }
        else {

            return response(['message'=> 'Set picture'],401);
        }

    }
    public function logout(Request $request) {
        Auth::user()->tokens()->where('id', Auth::user()->id)->delete();

        $response = [
            'message' => 'Logged out',
            'succes' => true
        ];
        return response ($response,200);
    }

    public function login(Request $request) {
        $fields = $request->validate([
            "identifiant" => 'string',
            "email" => 'string',
            "password" => 'required|string'
        ]);

        if(isset($fields['email']))
        {
            $user = User::where('email',$fields['email'])->first();
            if(!$user)
            {
                return response(['message'=> 'Email incorrect, check it'],401);
            }

            else
            {
                if(!Hash::check($fields['password'], $user->password))
                {
                    return response(['message'=> 'Password incorrect, check it'],401);
                }
                $token = $user->createToken('user_token')->plainTextToken;
                 $response = ['user' => $user,'token' => $token, 'message'=> 'You are login with your email'];

            return response($response,200);
            }
        }

        else if(isset($fields['identifiant']))
            {
                $identifiant = User::where('identifiant',$fields['identifiant'])->first();
                if(!$identifiant)
                {
                    return response(['message'=> 'Identifiant incorrect, check it'],401);
                }

                else
                {
                    if(!Hash::check($fields['password'], $identifiant->password))
                    {
                        return response(['message'=> 'Password incorrect, check it'],401);
                    }
                    $token = $identifiant->createToken('user_token')->plainTextToken;
                     $response = ['user' => $identifiant,'token' => $token, 'message'=> 'You are login with your identifiant'];

                return response($response,200);
                }
            }
        else {
            return response([
                'message'=> 'Put your email or identifiant to login'
            ],401);
        }
    }
// get  all users
    public function index()
    {
        return User::all();
    }

    // get user by id

    public function show()
    {   $id = Auth::user()->id;
        $user = User::find($id);
        if(!$user)
        {
            return response([
                'message'=> 'User not found'
            ],401);
        }
        return $user;
    }

    // delete user by id

    public function destroy()
    {   $id = Auth::user()->id;
        $user = User::destroy($id);
        if(!$user)
        {
            return response([
                'message'=> 'User not found'
            ],401);
        }
        return response([
            'message'=> 'User delete succefuly'
        ],200);
    }

    public function update_picture(Request $request)
    {   $id = Auth::user()->id;
        $picture = User::find($id);
        $image = $request->file('picture');
        if($request->hasFile('picture'))
        {   $filename = $picture->email.'.'.$image->getClientOriginalExtension();
            $image->move(public_path('/uploads/images',$filename));
            $picture->update($request->all());
            $response = ['user' => $picture, 'message'=> 'Picture update succefully'];
            return response($response,200);

        }

        else {
            return response([
                'message'=> 'Set picture to update your profil picture'
            ],401);
        }
    }

    public function update_profil(Request $request) {
        $id = Auth::user()->id;
        $user = User::find($id);
        fields = $request->validate([
            "identifiant" => 'required|string|unique:users,identifiant|min:5',
            "email" => 'required|string|unique:users,email',
            "sexe" => "required|string",
            "picture" => 'required|file',
            "birthday" => 'required|date'
    }

}
