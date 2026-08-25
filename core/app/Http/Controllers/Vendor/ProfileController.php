<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $vendor = authVendor();
        return view('Template::vendor.profile_setting', compact('pageTitle','vendor'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'image'       => ['nullable','image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ],[
            'firstname.required'=>'The first name field is required',
            'lastname.required'=>'The last name field is required'
        ]);

        $vendor = authVendor();

        if ($request->hasFile('image')) {
            try {
                $vendor->image = fileUploader($request->image, getFilePath('vendorProfile'), getFileSize('vendorProfile'), $vendor->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload image'];
                return back()->withNotify($notify);
            }
        }

        $vendor->firstname = $request->firstname;
        $vendor->lastname = $request->lastname;

        $vendor->address = $request->address;
        $vendor->city = $request->city;
        $vendor->state = $request->state;
        $vendor->zip = $request->zip;

        $vendor->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        return view('Template::vendor.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required','confirmed',$passwordValidation]
        ]);

        $vendor = authVendor();
        if (Hash::check($request->current_password, $vendor->password)) {
            $password = Hash::make($request->password);
            $vendor->password = $password;
            $vendor->save();
            $notify[] = ['success', 'Password changed successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }
}
