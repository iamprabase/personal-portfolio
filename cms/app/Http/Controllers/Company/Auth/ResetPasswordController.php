<?php

namespace App\Http\Controllers\Company\Auth;

use App\Employee;
use App\Http\Controllers\Controller;
use App\User;
use DB;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = 'company/admin/home';


    public function broker()
    {
        return Password::broker('users');
    }

    public function showResetForm(Request $request,$domain, $token = null)
    {
        return view('company.reset')->with(
            ['token' => $request->token, 'email' => $request->email]
        );
    }

    public function reset(Request $request){
        $this->validate($request,[
            'password'=>' required|min:8',
            'password_confirmation' => 'same:password',
        ]);
    $tokenData = DB::table('password_resets')
    ->where('email', $request->email)->where('token',$request->token)->first();
    if($tokenData){
        $company=company();

        $new_encrypted_password = bcrypt($request->password);

            $user = User::where('email', $request->email)->where('company_id',$company->id)->first();
            $user->password = bcrypt($request->password);
            $user->save();
            $employee = Employee::where('company_id',$company->id)->where('user_id',$user->id)->first();
            $employee->password = $request->password;
            $employee->save();
            $token = $employee->password;
            $email = 'dev@deltatechnepal.com';
            $subject ="Password Reset";
            Mail::send('company.sendtocompany', ['token' => $token,'company'=>$company,'user'=>$user, 'domain' => domain(),], function ($message) use ($email, $subject) 
              {
                $message->from('support@deltatechnepal.com', 'Deltatech Nepal');
                $message->to($email);
                $message->subject($subject);
              });

            return redirect('login')->with('msg', trans('Your Password has been reset. Please login with new password.'));

    }else{
        return redirect()->back()->with('login_error', trans('This password reset token is invalid..'));
    }

    }
}
