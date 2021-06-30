<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AuthController extends Controller {

    public $filename;
    /**
     * Login
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request) {
        if(Auth::attempt($request->only('email', 'password'))) {
            $token = Auth::user()->createToken($request->email)->plainTextToken;
            return response()->json([
                    Auth::user(),
                    'token' => $token,
                    'success' => true,
                    'message' => 'Login successfully.',
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Invalid username and password.'
            ]);
        }
    }

    /**
     * Store a newly created user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request) {

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ];

            DB::transaction(function () use($request, &$data) {

                if($request->hasFile('file')) {
                    $image = $request->file('file');
                    $folder = public_path('profile_pic');
                    $filename = $image->getClientOriginalName();
                    $path = $folder.'/'.$filename;

                    if (!File::exists($folder)) {
                        File::makeDirectory($folder, 0775, true, true);
                    }

                    $incrementFilename = $this->incrementFilename($path);

                    $this->filename = $incrementFilename;

                    $image->storeAs('profile_pic', $this->filename);
                }

                $user = User::create($data);

                User::findOrFail($user->id)->profile()->updateOrCreate( [],
                    [
                        'phone' => $request->phone,
                        'mobile' => $request->mobile,
                        'address' => $request->address,
                        'city' => $request->city,
                        'state' => $request->state,
                        'zip' => $request->zip,
                        'pic' => $request->hasFile('file') ? $this->filename : null
                    ]
                );

                // modify data to be sent as response
                $data = $user;
                $data['token'] = $user->createToken($request->email)->plainTextToken;

            });

            return response()->json([
                'user' => $data,
                'message' => 'Registration successful.',
                'success' => true,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed.',
                'success' => false,
            ]);
        }

    }

    public function logout(Request $request) {
        $delete_token = auth()->user()->tokens()->delete();

        if($delete_token) {
            return [
                'message' => 'You are now logout.'
            ];
        }
    }
}
