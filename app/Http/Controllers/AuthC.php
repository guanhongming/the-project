<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Twilio\Rest\Client;

use Illuminate\Support\Facades\Cache;


class AuthC extends Controller
{


        public function store(Request $request)
        {

            $validated = $request->validate([
                'username' => 'required|string|max:255|unique:users,name|regex:/^[A-Za-z0-9_]+$/',
                'phoneNumber' => 'required|string|unique:users,phone|max:15|regex:/^[0-9]{11}$/',
                'areaCode' => 'required|string|size:3',
                'password' => 'required|string|min:8',//change
            ]);

session(['registration_data' => $validated,]);


            return response()->json(['message' => 'OTP awaits'],200);
    }

public function sentOTP(Request $request){
    $sid = "AC6ed2e169470454f726e219a7cb2a3f87";
    $token = "ba16f3898f02e489a0bf59794ee326d7";
    $twilio = new Client($sid, $token);





    $otp = "123456";

    session([
        'otp' => $otp,

    ]);
    $validated = $request->validate([
        'phone' => 'required|string|max:15|regex:/^[0-9]{11}$/',
        'area' => 'required|string|size:3',
    ]);
    if(session()->has("registration_data")){
        $user = session('registration_data');
        $phone=$user["phoneNumber"];
        $area=$user["areaCode"];
    }elseif(session()->has("user_data")){
        $user = session('user_data');
        $phone=$user["phone"];
        $area=$user["area"];

    }
    if($phone!==$validated["phone"]){
        return response()->json(['message' => 'Phone number disagreement'], 401);
    }
    if($area!=='+86' || $area!==$validated['area']){
        return response()->json(['message' => 'Can not complete request (Area Code)'], 401);
    }
    $cooldownDurationInSeconds = 60;
    $cacheKey = 'otp_cooldown_' . $phone;


    if (Cache::has($cacheKey)) {

        return response()->json(['message' => 'Too Many Requests.'], 401);
    }

    try {

        $message = $twilio->messages->create(
            $validated['area'] . $validated['phone'],
            [
                "body" => "Your OTP is: " . $otp,
                "from" => "+17626002441",
           ]
        );
        Cache::put($cacheKey, true, now()->addSeconds($cooldownDurationInSeconds));
        if ($message->sid) {
        //if(true){
            return response()->json(['message' => 'OTP sent']);
        } else {
            return response()->json(['message' => 'Failed to send OTP'], 500);
        }
    } catch (\Exception $e) {

       // Log::error('Twilio API Error: ' . $e->getMessage());
        return response()->json(['message' => 'Failed to send OTP (e)'], 500);
    }
}



    public function verifyOtp(Request $request)
    {

        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if ($request->otp === session('otp')) {

            if(session()->has("registration_data")){
                $validated = session('registration_data');

                $user = new User([
                    'name' => $validated['username'],
                    'phone' => $validated['phoneNumber'],
                    'area_code' => $validated['areaCode'],
                    'password' => $validated['password']
                ]);
                $user->save();
                $ipAddress = $request->ip();
                $user->ip()->create([
                    'ip_address' => $ipAddress,
                ]);
                Auth::login($user);
                session()->forget(['registration_data']);
                session()->forget(['otp']);
                return response()->json(['message' => 'ok'], 200);

            }elseif(session()->has('user_data')){
                $user = session('user_data');
                Auth::login($user);
                $ipAddress = $request->ip();
                $user->ip()->create([
                    'ip_address' => $ipAddress,
                ]);
                return response()->json(['message' => 'ok'], 200);
            } else {
                return response()->json(['message' => 'Registration data not found in session'], 422);
            }

            //return response()->json(['name' => $user->name,'phone' => $user->phone,]);
        } else {
            return response()->json(['message' => 'Invalid OTP'], 422);
        }
    }











    public function auth(Request $request){
        $validated=$request->validate([
            'name'=>'required|string|min:4|regex:/^[A-Za-z0-9_]+$/',
            'password'=>'string|required|min:8'
        ]);

        $user = User::where('name', $validated['name'])->first();

    if ($user && $validated['password'] == $user->password) {
        $ipExists = $user->ip()->where('ip_address', $request->ip())->exists();
        if($ipExists){
        Auth::login($user);
            return response()->json(['message' => 'ok'], 200);

    }else{
        session(['user_data' => $user,]);
        return response()->json(['message' => 'otp awaits'], 403);

    }
}
    return response()->json(['message' => 'wrong'], 401);


    }
    public function pull(){

        if (Auth::check()) {

            $user = Auth::user();
            //Log::info('User is authenticated', ['user' => $user]);
            return response()->json(['name' => $user->name, "phone"=>$user->phone], 200);
        } else {
            return response()->json(['message' => 'wrong'], 401);
        }

    }

    public function logout(){
        auth()->logout();

        request()->session()->invalidate();
        request()->session()->regenerate();
        request()->session()->regenerateToken();
        return response()->json(['message' => 'ok'], 200);
    }


}
