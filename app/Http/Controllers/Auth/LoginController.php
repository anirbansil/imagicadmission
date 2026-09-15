<?php
 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
 
    public function login(Request $request) {
        //dd($request->all());

        $username = $request->email;
        $password = $request->password;
        if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
        //user sent their email
        Auth::attempt(['email' => $username, 'password' => $password]);
        } else {
            //they sent their username instead
            Auth::attempt(['email' => $username, 'password' => $password]);
        }

        if ( Auth::check() ) {
            //send them where they are going
            
            Session::flash('msg', "Welcome ".Auth::guard()->user()->name);            
            return redirect()->intended('dashboard');
        }else{
            Session::flash('error', "Email and password doesn't match!");
            return back();
        }        

        
    }
    
}
