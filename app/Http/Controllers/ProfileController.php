<?php

namespace App\Http\Controllers;

use App\Traits\UploadTrait;
use App\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use UploadTrait;

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        return view('auth.profile');
    }

    public function updateProfile(Request $request) {
        // form validate
        $request->validate([
            'name'=> 'required',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // get current user
        $user = User::findOrFail(auth()->user()->id);
        // set username
        $user->name = $request->input('name');
        
        // check if profile image has been uploaded
        if ($request->has('profile_image')) {
            // get image file
            $image = $request->file('profile_image');
            // make image name based on username and current timestamp
            $name = str_slug($request->input('name')).'_'.time();
            // define folder path
            $folder = '/uploads/images/';
            // make file path where image will be stored [folder path + file name + file extension]
            $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
            // upload the image
            $this->uploadOne($image, $folder, 'public', $name);
            // set user profile image path in database to filePath
            $user->profile_image = $filePath;
        }
        // persist record to database
        $user->save();

        // return user back and show success message
        return redirect()->back()->with(['status' => 'Profile update successfully.']);
    }
}
