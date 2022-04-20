<?php

namespace App\Http\Controllers;

// import  User model,response and hash facades
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\AfterRegister;
use App\Mail\forgotPassword;

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
            $token_user = rand();
            $user = User::create([
                "identifiant" => $fields['identifiant'],
                "email" => $fields['email'],
                "password" => bcrypt($fields['password']),
                "sexe" => $fields["sexe"],
                "birthday" => $fields['birthday'],
                "picture" => $filename,
                "api_key" => $token_user
            ]);

            $response = [
                'message' => 'Register succefuly, please check your email to validate your compte for create task',
                'user' => $user,
                'succes' => true
            ];
            Mail::to($request->email)->send(new AfterRegister($user));
            return response($response,201);
        }
        else {

            return response(['message'=> 'Set picture to create your profil'],401);
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
        // check if compte is validate
            $check = User::where('email',$fields['email'])->first();
            if($check->isValidate == false)
            {
                return response([
                    'message'=> 'Validate your compte after to login'
                ],401);
            }
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
        $fields = $request->validate([
            "identifiant" => 'string|unique:users,identifiant|min:5',
            "email" => 'string|unique:users,email',
            "sexe" => "string",
            "birthday" => 'date']);
        $user->update($request->all());
        $response = ['user' => $user, 'message'=> 'Profil update succefully'];
            return response($response,200);
    }

    public function reset_password(Request $request) {
        $fields = $request->validate([
            "old_password" => "required|string",
            "new_password" => "required|string|min:5|confirmed"
        ]);
        $id = Auth::user()->id;
        $user = User::find($id);
        if(!Hash::check($fields['old_password'], $user->password)){
            return response(['message'=> 'Bad old password, try again'],401);
        }
        $user->password = $request->new_password;
        $user->save();
        return response(['message'=> 'Password update succefuly'],200);
    }

    public function activation_compte($id) {
        $find = User::where('api_key',$id)->first();
        if($find)
        {
            $find->isValidate = true;
            $find->save();
            $token = $find->createToken('user_token')->plainTextToken;
            return response([
                'message'=> 'Compte validate succefuly!Thanks',
                'token' => $token
            ],200);
        }
        else
        {
            return response([
                'message'=> 'Unauthorized'
            ],401);
        }
    }

    public function receive_email_to_forgot_password(Request $request) {
        $email = $request->email;
        $user = User::where('email',$email)->first();
        if($user)
        {   Mail::to($request->email)->send(new forgotPassword($user->identifiant,$user->api_key));
            return response([
                'message'=> 'Check your mail, to reset your password'
            ],200);
        }
        return response([
            'message'=> 'Bad email!'
        ],401);
    }

    public function forgot_password(Request $request, $token)
    {
        $fields = $request->validate([
            "new_password" => "required|string|min:5|confirmed"
        ]);
        $user = User::find($token);
        if(!$user){
            return response(['message'=> 'Unauthorized'],401);
        }
        $user->password = $request->new_password;
        $user->save();
        return response(['message'=> 'Password update succefuly,login now','user' => $user],200);
    }

}
