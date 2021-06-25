<?php

namespace App\Http\Controllers;

use App\Company;
use App\Contactus;
use App\Employee;
use App\RequestQuotes;
use App\User;
use DB;
use Illuminate\Http\Request;
use Mail;

class FrontController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function index(Request $request)
    {

        return view('front.index');
    }

    public function feature(Request $request)
    {

        return view('front.feature');
    }

    public function pricing(Request $request)
    {

        return view('front.pricing');
    }

    public function requestdemo(Request $request)
    {

        return view('front.request-demo');
    }

    public function contactus(Request $request)
    {
        return view('front.contact-us');
    }

    public function privacy(Request $request)
    {

        return view('front.privacy-policy');
    }

    public function login(Request $request)
    {

        return view('front.login');
    }

    public function blog(Request $request)
    {

        return view('front.blog');
    }

    public function salestrackingappinnepal(Request $request)
    {

        return view('front.sales-tracking-app-in-nepal');
    }

    public function leadmanagementsoftwareinnepal(Request $request)
    {

        return view('front.lead-management-software-in-nepal');
    }

    public function performancemeasuringsoftwareinnepal(Request $request)
    {

        return view('front.performance-measuring-software-in-nepal');
    }

    public function fieldsalesmanagementsoftwareinnepal(Request $request)
    {

        return view('front.field-sales-management-software-in-nepal');
    }

    public function salesmanagementsoftwareinnepal(Request $request)
    {

        return view('front.sales-management-software-in-nepal');
    }

    public function manageorderinnepal(Request $request)
    {

        return view('front.manage-order-in-nepal');
    }


    public function realtimegpstracking(Request $request)
    {

        return view('front.real-time-gps-tracking');
    }

    public function maintainattendance(Request $request)
    {

        return view('front.maintain-attendance');
    }

    public function managesalesexpense(Request $request)
    {

        return view('front.manage-sales-expense');
    }

    public function measuresalesperformance(Request $request)
    {

        return view('front.measure-sales-performance');
    }

    public function leaveapplication(Request $request)
    {

        return view('front.leave-application');
    }

    public function tasksassignment(Request $request)
    {

        return view('front.tasks-assignment');
    }

    public function manageclients(Request $request)
    {

        return view('front.manage-clients');
    }

    public function markclientlocation(Request $request)
    {

        return view('front.mark-client-location');
    }

    public function manageenquiries(Request $request)
    {

        return view('front.manage-enquiries');
    }

    public function managecollections(Request $request)
    {

        return view('front.manage-collections');
    }

    public function manageorders(Request $request)
    {

        return view('front.manage-orders');
    }

    public function worksoffline(Request $request)
    {

        return view('front.works-offline');
    }

    public function adddailyremarks(Request $request)
    {

        return view('front.add-daily-remarks');
    }


    public function bestsalestrackingapplication(Request $request)
    {

        return view('front.best-sales-tracking-application');
    }

    public function traveldistancecalculator(Request $request)
    {

        return view('front.travel-distance-calculator');
    }

    public function monthlysalestarget(Request $request)
    {

        return view('front.monthly-sales-target');
    }

    public function announcement(Request $request)
    {

        return view('front.announcement');
    }

    public function manageproducts(Request $request)
    {

        return view('front.manage-products');
    }

    public function meetingrecords(Request $request)
    {

        return view('front.meeting-records');
    }

    public function salesemployeereports(Request $request)
    {

        return view('front.sales-employee-reports');
    }

    public function verify(Request $request)
    {
        $company = Company::findOrFail($request->id);
        // $company = Company::findOrFail($request->id);

        // print_r($company);
        // die;
        $msg = "";
        if ($company->verify_token == $request->token && $company->is_verified == 0) {
            $company->is_verified = 1;
            $saved = $company->save();
            if ($saved) {
                // function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
                // {
                //     $pieces = [];
                //     $max = mb_strlen($keyspace, '8bit') - 1;
                //     for ($i = 0; $i < $length; ++$i) {
                //         $pieces [] = $keyspace[random_int(0, $max)];
                //     }
                //     return implode('', $pieces);
                // }
                // $new_password = random_str(8);
                // $new_encrypted_password = bcrypt($new_password);

                $user = User::where('company_id',$company->id)->where('email', $company->contact_email)->first();
                $employee = Employee::where('company_id',$company->id)->where('user_id',$user->id)->first();
                $new_password = $employee->password;
                if ($user) {
                    $emails = $user->email;
                    $subject = 'Verification Successful';
                    Mail::send('company.thankyou', ['email' => $user->email, 'password' => $new_password, 'domain' => $company->domain], function ($message) use ($emails, $subject) {
                        $message->from('support@deltasalesapp.com', 'Deltatech Nepal');
                        $message->to($emails);
                        $message->bcc('dev@deltatechnepal.com');
                        $message->subject($subject);
                    });
                }
            }
            $msg = 'Your email has been verified. Please check your email for the username and password.';
            //$request->session()->flash('verify', 'Your email has been verified. Please check your email for the username and password.');
        }
        return redirect('https://' . $company->domain . '.' . config('app.domain') . '/login')->with('verify', $msg);
    }

    public function requestquote(Request $request)
    {
        $customMessages = [
            'company_name.required' => 'The Company field is required.',
            'name.required' => 'The Name field is required.',
            'email.required' => 'The email field is required',
            'phone_no.required' => 'The Mobile number is required',
            'phone_no.between' => 'The Number must be between 7 to 20 letters',
            'captcha' => 'Invalid captcha code',
        ];


        $this->validate($request, [
            'name' => 'required',
            'company_name' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone_no' => 'required|between:7,20',
            'captcha' => 'required|captcha',
        ], $customMessages);

        // $reqquote = new RequestQuotes();
        // $save_quote = $reqquote->save();

        $save_quote = DB::table('request_quote')->insert([
            'name' => $request->get('name'),
            'companyName' => $request->get('company_name'),
            'email' => $request->get('email'),
            'mobile' => $request->get('phone_no'),
            'skype' => $request->get('skype_id'),
        ]);

        if ($save_quote) {
            $visitor_email = $request->get('email');
            $subject_to_visitor = 'Thank You';
            Mail::send('company.thankyou_quote', ['email' => $visitor_email, 'name' => $request->get('name')], function ($message) use ($visitor_email, $subject_to_visitor) {
                $message->from('support@deltasalesapp.com', 'Deltatech Nepal');
                $message->to($visitor_email);
                $message->subject($subject_to_visitor);
            });

            $subject_to_info = 'DeltaSales App : Demo Request';
            $info_emails = ['info@deltatechnepal.com', 'sales@deltatechnepal.com'];
            Mail::send('company.requestdemo_new', [
                'name' => $request->get('name'),
                'companyName' => $request->get('company_name'),
                'email' => $request->get('email'),
                'mobile' => $request->get('phone_no'),
                'skype' => $request->get('skype_id'),
            ], function ($message) use ($visitor_email, $subject_to_info, $info_emails) {
                $message->from($visitor_email, 'New Demo Request');
                $message->to($info_emails);
                $message->bcc('dev@deltatechnepal.com');
                $message->subject($subject_to_info);
            });

            if (Mail::failures()) {
                return redirect()->back()->with('error_message', 'Something went wrong');
            } else {
                return redirect()->back()->with('success_message', 'Thank you for contacting us. We will get back to you shortly.');
            }
        }
        return redirect()->back()->with('error_message', 'Something went wrong');
    }


    public function contact_us(Request $request)
    {
        // dd($request->all());
        $customMessages = [
            'name.required' => 'The Name field is required.',
            'email.required' => 'The email field is required',
            'phone_no.required' => 'The Mobile number is required',
            'phone_no.between' => 'The Number must be between 7 to 20 letters',
            'subject.required' => 'The subject field is required',
            'message.required' => 'The message field is required',
            'captcha' => 'Invalid captcha code',
        ];


        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'phone_no' => 'required|between:7,20',
            'subject' => 'required',
            'message' => 'required',
            'captcha' => 'required|captcha',
        ], $customMessages);

        $save_enquiry = DB::table('contactus')->insert([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'mobile' => $request->get('phone_no'),
            'subject' => $request->get('subject'),
            'message' => $request->get('message'),
        ]);

        if ($save_enquiry) {
            $visitor_email = $request->get('email');
            $subject_to_visitor = 'Thank You';
            Mail::send('company.thankyou_contact', ['email' => $visitor_email, 'name' => $request->get('name')], function ($message) use ($visitor_email, $subject_to_visitor) {
                $message->from('support@deltasalesapp.com', 'Deltatech Nepal');
                $message->to($visitor_email);
                $message->subject($subject_to_visitor);
            });

            $subject_to_info = 'DeltaSales App : Enquiry';
            $info_emails = ['info@deltatechnepal.com', 'sales@deltatechnepal.com'];
            Mail::send('company.enquiry_new', [
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'mobile' => $request->get('phone_no'),
                'subject' => $request->get('subject'),
                'description' => $request->get('message'),
            ], function ($message) use ($visitor_email, $subject_to_info, $info_emails) {
                $message->from($visitor_email, 'New Enquiry');
                $message->to($info_emails);
                $message->bcc('dev@deltatechnepal.com');
                $message->subject($subject_to_info);
            });

            if (Mail::failures()) {
                return redirect()->back()->with('error_message', 'Something went wrong');
            } else {
                return redirect()->back()->with('success_message', 'Thank you for contacting us. We will get back to you shortly.');
            }
        }
        return redirect()->back()->with('error_message', 'Something went wrong');

    }

    public function refreshCaptcha()
    {
        $data = captcha_img();
        // // $data['default'];
        // dd($data['default']);
        return $data;
        // echo $test; die();
        // return response()->json(['captcha'=> captcha_img()]);
    }

    public function checksubdomain(Request $request)
    {
        $subdomain = $request->get('subdomain');
        $exists = DB::table('companies')->where('domain', $subdomain)->first();
        if (!empty($exists)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function go_to_app(Request $request)
    {
        $subdomain = $request->get('subdomain');
        $company = DB::table('companies')->select('domain')->where('domain', $subdomain)->first();
        return redirect('http://' . $company->domain . '.' . config('app.domain') . '/login');
    }
}