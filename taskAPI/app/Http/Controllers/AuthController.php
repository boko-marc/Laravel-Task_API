<?php
/**
 *This controller contain all users functions
 *
 */
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

    /**Create user account and send validation account email
     * @param Request $request
     * @return \Illuminate\Http\Response : message and user create
      */
    public function register(Request $request)
    {
        $fields = $request->validate([
            "identifiant" => 'required|string|unique:users,identifiant|min:5',
            "email" => 'required|string|unique:users,email',
            "password" => 'required|string|confirmed|min:8',
            "sexe" => "required|string",
            "picture" => 'required|file',
            "birthday" => 'required|date'
        ]);
        if($request->hasFile('picture'))
        {   $filename = rand().'.'.$fields['picture']->getClientOriginalExtension();
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
                'user' => $user
            ];
            Mail::to($request->email)->send(new AfterRegister($user));
            return response($response,201);
        }
        else {

            return response(['message'=> 'Set picture to create your profil'],401);
        }

    }

    /**
     * This function check  user email or identifiant and password to login user and create token
     * @param Request $request
     * @return \Illuminate\Http\Response, user : user login , token
     */
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

    /**This function return all users create
     * @return all users
     */
// get  all users
    public function index()
    {
        return User::all();
    }

    // get user by id
/**This function show user connected
 * @return \Illuminate\Http\Response
 */
    public function show()
    {
        $user = Auth::user();
        if(!$user)
        {
            return response([
                'message'=> 'User not found'
            ],401);
        }
        return $user;
    }

    // delete user by id
/**This function delete user connected account */
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
/**This function update user picture
 * @param Request $request
 *@return $user :user account update, \Illuminate\Http\Response
 */
    public function updatePicture(Request $request)
    {
        $image = $request->file('picture');
        if($request->hasFile('picture'))
        {   $filename = rand().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('/uploads/images',$filename));
            Auth::user()->picture = $filename;
            Auth::user()->save();
            $response = ['user' => $Auth::user(), 'message'=> 'Picture update succefully'];
            return response($response,200);

        }

        else {
            return response([
                'message'=> 'Set picture to update your profil picture'
            ],401);
        }
    }

/**This function update user  profil informations
 * @param Request $request
 *@return $user :user account update, \Illuminate\Http\Response
 */

    public function updateProfil(Request $request) {
        $fields = $request->validate([
            "identifiant" => 'string|unique:users,identifiant|min:5',
            "email" => 'string|unique:users,email',
            "sexe" => "string",
            "birthday" => 'date']);

            $user = Auth::user();
            $user->update($request->all());
            $response = ['user' => $user, 'message'=> 'Profil update succefully'];
            return response($response,200);
    }


    /**This function reset user password
 * @param Request $request
 *@return  \Illuminate\Http\Response
 */
    public function resetPassword(Request $request) {
        $fields = $request->validate([
            "old_password" => "required|string",
            "new_password" => "required|string|min:8|confirmed"
        ]);
        $user = Auth::user();
        if(!Hash::check($fields['old_password'], $user->password)){
            return response(['message'=> 'Bad old password, try again'],401);
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        return response(['message'=> 'Password update succefuly'],200);
    }

    /**This function active user account and create user token
 * @param $id : user api_key receive after create account
 *@return $token :user account token, \Illuminate\Http\Response
 */
    public function activationCompte($id) {
        $user = User::where('api_key',$id)->first();
        if($user)
        {
            $user->isValidate = true;
            $user->save();
            $token = $user->createToken('user_token')->plainTextToken;
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
/**This function send email at user after the user forget password
 * @param \Illuminate\Http\Request $request
 *@return \Illuminate\Http\Response
 */
    public function receiveEmailToForgotPassword(Request $request) {
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
/**This function change user passsword
 * @param \Illuminate\Http\Request $request, $token : user api_key receive after send email to change password
 *@return $user :user account update, \Illuminate\Http\Response
 */
    public function changePassword(Request $request, $token)
    {
        $fields = $request->validate([
            "new_password" => "required|string|min:8|confirmed"
        ]);
        $user = User::find($token);
        if(!$user){
            return response(['message'=> 'Unauthorized'],401);
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        return response(['message'=> 'Password update succefuly,login now','user' => $user],200) ;
    }

}
