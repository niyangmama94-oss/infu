<?php

namespace App\Http\Controllers\Influencer\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Influencer;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    public function showRegistrationForm() {
        $pageTitle   = "Sign Up";
        return view('Template::influencer.auth.register', compact('pageTitle'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        $general            = gs();
        $passwordValidation = Password::min(6);
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $agree = 'nullable';
        if ($general->agree) {
            $agree = 'required';
        }

        $validate     = Validator::make($data, [
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|string|email|unique:influencers',
            'password'  => ['required', 'confirmed', $passwordValidation],
            'captcha'   => 'sometimes|required',
            'agree'     => $agree
        ],[
            'firstname.required'=>'The first name field is required',
            'lastname.required'=>'The last name field is required'
        ]);

        return $validate;

    }

    public function register(Request $request) {


        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $exist = Influencer::where('mobile', $request->mobile_code . $request->mobile)->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return back()->withNotify($notify)->withInput();
        }

        event(new Registered($influencer = $this->create($request->all())));

        $this->guard()->login($influencer);

        return $this->registered($request, $influencer)
        ?: redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data) {
        $general = gs();

        $referBy = session()->get('reference');
        if ($referBy) {
            $referUser = Influencer::where('username', $referBy)->first();
        } else {
            $referUser = null;
        }
        //User Create
        $influencer               = new Influencer();
        $influencer->email        = strtolower(trim($data['email']));
        $influencer->password     = Hash::make($data['password']);
        $influencer->firstname = $data['firstname'];
        $influencer->lastname  = $data['lastname'];
        $influencer->ref_by       = $referUser ? $referUser->id : 0;

        $influencer->status = Status::INFLUENCER_ACTIVE;
        $influencer->kv     = $general->influencer_kv ? Status::KYC_UNVERIFIED : Status::KYC_VERIFIED;
        $influencer->ev     = $general->ev ? Status::UNVERIFIED : Status::VERIFIED ;
        $influencer->sv     = $general->sv ? Status::UNVERIFIED : Status::VERIFIED ;
        $influencer->ts     = Status::OFF;
        $influencer->tv     = Status::VERIFIED;
        $influencer->save();

        $adminNotification                = new AdminNotification();
        $adminNotification->influencer_id = $influencer->id;
        $adminNotification->title         = 'New Influencer registered';
        $adminNotification->click_url     = urlPath('admin.influencers.detail', $influencer->id);;
        $adminNotification->save();

        //Login Log Create
        $ip        = getRealIP();
        $exist     = UserLogin::where('influencer_id', $ip)->first();
        $userLogin = new UserLogin();

        //Check exist or not
        if ($exist) {
            $userLogin->longitude    = $exist->longitude;
            $userLogin->latitude     = $exist->latitude;
            $userLogin->city         = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude    = @implode(',', $info['long']);
            $userLogin->latitude     = @implode(',', $info['lat']);
            $userLogin->city         = @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country      = @implode(',', $info['country']);
        }

        $userAgent                = osBrowser();
        $userLogin->influencer_id = $influencer->id;
        $userLogin->user_ip       = $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os      = @$userAgent['os_platform'];
        $userLogin->save();

        return $influencer;
    }

    protected function guard() {
        return auth()->guard('influencer');
    }

    public function checkUser(Request $request) {
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = Influencer::where('email', $request->email)->exists();
            $exist['type'] = 'email';
        }
        if ($request->mobile) {
            $exist['data'] = Influencer::where('mobile', $request->mobile)->exists();
            $exist['type'] = 'mobile';
        }
        if ($request->username) {
            $exist['data'] = Influencer::where('username', $request->username)->exists();
            $exist['type'] = 'username';
        }
        return response($exist);
    }

    public function registered() {
        return to_route('influencer.home');
    }

}
