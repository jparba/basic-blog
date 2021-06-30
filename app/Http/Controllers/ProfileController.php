<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $profile = Profile::with('user')->where('user_id', Auth::id())->first();

        if($profile) {
            $user = $profile->user->toArray();
            unset($profile->user);

            $user = array_merge($user, $profile->toArray());
        }

        return response()->json($user, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        try{
            DB::transaction(function () use($request) {
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

                $user_data = [
                    'name' => $request->name,
                    'email' => $request->email,
                ];
                $profile_data = [
                    'phone' => $request->phone,
                    'mobile' => $request->mobile,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip' => $request->zip,
                    'pic' => $request->hasFile('file') ? $this->filename : $request->pic
                ];

                $profile = Profile::find(Auth::id());
                $profile->update($profile_data);
                $profile->user()->update($user_data);

            });

            return response()->json([
                'success' => true,
                'message' => 'Profile updated.'
            ], 200);

        }catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update.'
            ]);
        }
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show($name) {
        $user = User::with('profile')->where('name', $name)->first();

        if($user) {
            $profile = $user->profile->toArray();
            unset($user->profile);

            $user = array_merge($user->toArray(), $profile);
        }

        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profile $profile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
