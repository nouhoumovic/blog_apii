<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    use ApiResponser;

  public function register(Request $request){


        $attr = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:4|confirmed',
        ]);
        $user = User::create([
            'name' => $attr['name'],
            'password' => bcrypt($attr['password']),
            'email' => $attr['email'],
        ]);

        //return user & token in response
        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ], 200);



  }

  public function login(Request $request){

        $attr = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if (!Auth::attempt($attr)) {
            return $this->error('Credentials not match', 401);
        }

        //return user & token in response
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);

  }

  public function CurrentUser(Request $request){
    try{
        return response([
            'user' => auth()->user()
        ], 200);
    }catch (\Exception $exception){
        return $this->error($exception->getMessage(),500, $request->toArray());
    }

}

  public function logout(){
    auth()->user()->tokens()->delete();
    return response([
        'message'=> 'logout success.'
    ],200);
  }

   // update user
   public function update(Request $request)
   {
       $attrs = $request->validate([
           'name' => 'required|string'
       ]);

       $image = $this->saveImage($request->image, 'profiles');

       auth()->user()->update([
           'name' => $attrs['name'],
           'image' => $image
       ]);

       return response([
        'message' => 'User updated.',
        'user' => auth()->user()
    ],200);

    //    return response([
    //        'message' => 'User updated.',
    //        'user' => auth()->user()
    //    ], 200);
   }
}
