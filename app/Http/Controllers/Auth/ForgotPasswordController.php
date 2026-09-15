<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Validator;
use DB;
use Session;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    // use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showForm() {
      $title = 'Forgot password';
      return view('auth.forgot-form', compact('title'));
    }

    public function sendResetLink(Request $request) {
      $validator = Validator::make($request->all(), [
        'email' => 'required|email',
      ]);

      if ($validator->fails()) {
        return redirect()->back()
          ->withErrors($validator)
          ->withInput();
      }

      $user = User::where('email', $request->email)->first();

      if ($user) {
        $to = $request->email;
        $subject = "Reset password";
        $txt = '
          <!DOCTYPE html>
          <html>
            <head>
              <title>Indian Railways</title>
            </head>
            <body style="font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; background: #F5F5F5;">
              <table class="wrapper" style="max-width: 600px; width: 100%; margin: 0 auto;background: #fff;padding: 20px 0px 0px 0px;border:none;border-radius: 0px; border-collapse:collapse;" cellpadding="0" cellspacing="0">
                <tr>
                  <td>
                    <table class="bodyContent" style="border-radius:0px;border-spacing: 0;padding-top: 0px; width: 100%; border-collapse:collapse;">
                      <tr style="text-align: center;">
                        <td colspan="2">
                          <a href="https://pixeldevstudio.in/indian_railway_religious/" class="logo" style="text-decoration: none;display: inline-block; margin: 6px 0;">
                            <img src="https://pixeldevstudio.in/indian_railway_religious/public/images/logo.png" style="width: 100%; max-width: 175px;">
                          </a>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 8px 10px; width: 100%;">
                          <p style="margin: 0;color: #45596f;font-size: 19px;line-height: 30px;font-weight: bold;padding: 30px 0 0 0;text-align: center;">Change your password and login</p>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 8px 10px; width: 100%;vertical-align: top; text-align: center;">
                            <a href="https://pixeldevstudio.in/indian_railway_religious/reset-password?id='.base64_encode($user->id).'" style="display: inline-block;border-radius: 8px;background: #08c185;color: #fff;font-weight: 500;font-size: 16px;text-decoration: none;padding: 9px 18px;text-transform: capitalize;border-radius: 15px;margin-bottom: 25px;">click here to change password </a>
                        </td>              
                       

                      
                      </tr>

                    </table>
                  </td>
                </tr>     
              </table>
            </body>
          </html>

        ';
        $headers = "From: <info@pixeldevstudio.in>";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html\r\n";
        mail($to,$subject,$txt,$headers);
        
        Session::flash('msg', 'Reset password link has been sent to your email');
        return redirect('/');
      } else {
        Session::flash('error', 'Email Id doesnot matched!');
        return redirect()->back();
      }

    }

    public function resetPassword() {
      $title = 'Reset password';
      return view('auth.reset-password', compact('title'));
    }

    public function resetPasswordStore(Request $request) {
        $validator = Validator::make($request->all(), [
          'password' => 'required|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            return redirect()->back()

                ->withErrors($validator)

                ->withInput();

        }
        $id = base64_decode(request()->get('id'));
        try{          
          $user = User::find(str_replace("'", "", $id));
          $user->password = bcrypt($request->password);
          $user->save();
          
          Session::flash('resmsg', 'Your password has been succesfully updated');
          return redirect('/');
          
        }catch (\Exception $e) {
            Session::flash('error', $e->getMessage());

            return redirect()->back();

        }

    }
}
