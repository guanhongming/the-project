<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;


class AuthC extends Controller
{


        public function store(Request $request)
        {
            // Validate the incoming request data
            $validated = $request->validate([
                'username' => 'required|string|max:255',
                'phoneNumber' => 'required|string|unique:users,phone|max:15|regex:/^[0-9]{10}$/',
                'areaCode' => 'required|string|max:3',
                'password' => 'required|string|min:8',//change
            ]);

session(['registration_data' => $validated,]);


            return response()->json(['message' => 'OTP awaits']);
    }

public function sentOTP(Request $request){
    $otp = "000000";

    session([
        'otp' => $otp,

    ]);
    $validated = $request->validate([
        'phone' => 'required|string|unique:users,phone|max:15|regex:/^[0-9]{10}$/',
        'area' => 'required|string|max:3',
    ]);
    return response()->json(['message' => 'OTP sent']);
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
                Auth::login($user);
                session()->forget(['registration_data']);
                session()->forget(['otp']);

            } else {
                return response()->json(['message' => 'Registration data not found in session'], 422);
            }
            return response()->json(['message' => 'ok'], 200);
            //return response()->json(['name' => $user->name,'phone' => $user->phone,]);
        } else {
            return response()->json(['message' => 'Invalid OTP'], 422);
        }
    }











    public function auth(Request $request){
        $validated=$request->validate([
            'name'=>'required|string|min:5',
            'password'=>'string|required'
        ]);

        $user = User::where('name', $validated['name'])->first();
Log::info(Auth::login($user));
    if ($user && $validated['password'] == $user->password) {
        Auth::login($user);
            return response()->json(['message' => 'ok'], 200);

    }
    return response()->json(['message' => 'wrong'], 401);


    }

    public function logout(){
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerate();
        return redirect()->route('dashboard')->with('success','logged out');
    }

    public function login(){
        //return redirect('');

    }
    public function Identity(){
        //return redirect('');

    }
}
