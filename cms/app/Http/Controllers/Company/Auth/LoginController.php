<?php

namespace App\Http\Controllers\Company\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Mail;
use App\User;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('company.login');
    }

    public function showForgotPassword()
    {
        return view('company.forgotpassword');
    }

    public function handleLogin(Request $request)
    {
        $customMessages = [
            'username.required' => 'Email or Mobile No. is required.',
            'password' => 'Password is required',
        ];
        
        $validatedData = $request->validate([
            'username' => 'required|max:255',
            'password' => 'required|string',

        ], $customMessages);

        $isRemember = $request->input('is_remember');
        $field = 'email';
            if (is_numeric($request->input('username'))) {
                $field = 'phone';
            } elseif (filter_var($request->input('username'), FILTER_VALIDATE_EMAIL)) {
                $field = 'email';
            }
            $company=company();

        //$username = $request->input('username');
       // $password = $request->input('password');

        $credentials = [
            $field => $request->input('username'),
            'password' => $request->input('password'),
            'company_id' => $company->id,
            'deleted_at' => null
        ];

        if(auth()->attempt($credentials)) {
            if( auth()->user()->managers->first() && $company->is_active==1){
              $msg = 'Sorry, your account has been disabled.';
              auth()->logout();
              return redirect()->back()->withInput()->withErrors(['login_error' => [$msg]]);
            }elseif(!auth()->user()->managers->first() && $company->is_active==1){
              auth()->logout();
              return redirect()->back()->withInput()->withErrors(['login_error' => ['Account has been disabled.']]);
            }elseif(!auth()->user()->managers->first() && $company->is_active==0){
              auth()->logout();
              return redirect()->back()->withInput()->withErrors(['login_error' => ['Your subscription has ended.']]);
            }
            // elseif(auth()->user()->employees->first()->is_admin==1){
            //   if(auth()->user()->isCompanyManager()->is_owner==0 && $company->is_active==0){
            //     auth()->logout();
            //     return redirect()->back()->withInput()->withErrors(['login_error' => ['Your subscription has ended.']]);
            //   }
            // }
            if(auth()->user()->is_active != 2){
                auth()->logout();
                return redirect()->back()->withInput()->withErrors(['login_error' => ['Your account has been deactivated']]);
            }else{
                if(auth()->user()->isCompanyManager() || auth()->user()->isCompanyEmployee()){
                    activity()->log('Logged In');
                    return redirect()->intended(route('company.admin.home', ['domain' => domain()]));
                }
                else{
                    auth()->logout();
                    return redirect()->back()->withInput()->withErrors(['login_error' => ['You are not authorized user']]);
                }
            }
            return redirect()->route('dashboard');       
        }       
        
        return redirect()->back()->withInput()->withErrors(['login_error' => ['Wrong '.$field.' or password']]);
    }

    public function logout()
    {
        if(auth()->user()){
            activity()->log('Logged Out');
        }
        auth()->logout();
        return redirect()->route('company.login', ['domain' => domain()]);
    }

    public function forgotPassword(Request $request)
    {
      $customMessages = [
       'username.required' => 'Email or Mobile No. is required.',
      ];
        
      $validatedData = $request->validate([
        'username' => 'required|max:255',
      ], $customMessages);
         
      $field = '';
      if (is_numeric($request->input('username'))) 
      {
        $field = 'phone';
      } 
      elseif (filter_var($request->input('username'), FILTER_VALIDATE_EMAIL)) 
      {
        $field = 'email';
      }
      else{
        return redirect()->back()->withErrors(['login_error' => trans('Please Enter Correct Email or Phone')]);
      }
            
      $company=company();
      $user=User::where($field, $request->input('username'))
                    ->where('company_id',$company->id)->first();

      if($user){
        if($user->is_active==2)
        {
          $email=$user->email;
          $companymanager=$user->isCompanyManager();
          //echo $user->isCompanyManager();
          //die;
          if($companymanager){

            DB::table('password_resets')->insert([
              'email' => $email,
              'token' => str_random(60),
              'created_at' => Carbon::now()
            ]);
            $tokenData = DB::table('password_resets')
    ->where('email', $email)->first();

if ($this->sendResetEmail($email, $tokenData->token,$company->id,$company->domain)) {
    return redirect()->back()->with('msg', trans('A reset link has been sent to your email address.'));
} else {
    return redirect()->back()->withErrors(['login_error' => trans('A Network Error occurred. Please try again.')]);
}


          }else
         return redirect()->back()->withInput()->withErrors(['login_error' => ['Please Contact your Administrator']]);
         
        }
        else
        {
          return redirect()->back()->withInput()->withErrors(['login_error' => ['Your account is inactive. Please Contact your Administrator']]);
        }
      }
      else
      {
        return redirect()->back()->withInput()->withErrors(['login_error' => ['No record with given '.$field.' is found']]);
      }
}

private function sendResetEmail($email, $token,$company_id, $company_domain)
{
//Retrieve the user from the database
$user = DB::table('users')->where('email', $email)->where('company_id',$company_id)->select('name', 'email')->first();
//Generate, the password reset link. The token generated is embedded in the link
$link = config('base_url') . 'password/reset/' . $token . '?email=' . urlencode($email);

$subject = $company_domain . " - Reset Link";

              Mail::send('company.send', ['content' => $link, 'domain' => domain(),], function ($message) use ($email, $subject) 
              {
                $message->from('support@deltatechnepal.com', 'Deltatech Nepal');
                $message->to($email);
                $message->bcc('dev@deltatechnepal.com');
                $message->subject($subject);
              });
      return true;
  }

  public function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
            {
                $pieces = [];
                $max = mb_strlen($keyspace, '8bit') - 1;
                for ($i = 0; $i < $length; ++$i) {
                    $pieces [] = $keyspace[random_int(0, $max)];
                }
                return implode('', $pieces);
            }

}
