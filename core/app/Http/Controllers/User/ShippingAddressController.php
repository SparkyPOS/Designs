<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use Illuminate\Http\Request;
use App\Models\ShippingAddress;
use App\Http\Controllers\Controller;

class ShippingAddressController extends Controller {
    public function index() {
        $pageTitle   = "Shipping Address";
        $addresseses = ShippingAddress::where('user_id', auth()->id())->get();
        $info        = json_decode(json_encode(getIpInfo()), true);
        $mobileCode  = isset($info['code']) ? implode(',', $info['code']) : '';
        $countries   = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view('Template::user.shipping_address', compact('addresseses', 'pageTitle', 'countries', 'mobileCode'));
    }

    public function store(Request $request, $id = NULL) {
        $countryData  = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'title'        => 'required|max:255|unique:shipping_addresses,title,' . $id . ',id',
            'firstname'    => 'required|max:255',
            'lastname'     => 'required|max:255',
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'country_code' => 'required|in:' . $countryCodes,
            'mobile'       => 'required|regex:/^([0-9]*)$/',
            'email'        => 'required|string|email|max:255',
            'city'         => 'required|string|max:255',
            'state'        => 'required|string|max:255',
            'zip'          => 'required|string|max:255',
            'country'      => 'required|in:' . $countries,
            'address'      => 'required|string|max:255',
            'default'      => 'nullable',
        ]);

        if ($id) {
            $address  = ShippingAddress::where('user_id', auth()->id())->findOrFail($id);
            $notify[] = ['success', 'Address updated successfully'];
        } else {
            $address  = new ShippingAddress();
            $address->user_id        = auth()->id();

            $notify[] = ['success', 'Address saved successfully'];
        }

        $address->title        = $request->title;
        $address->firstname    = $request->firstname;
        $address->lastname     = $request->lastname;
        $address->mobile_code  = $request->mobile_code;
        $address->country_code = $request->country_code;
        $address->mobile       = $request->mobile;
        $address->email        = $request->email;
        $address->city         = $request->city;
        $address->state        = $request->state;
        $address->zip          = $request->zip;
        $address->country      = $request->country;
        $address->address      = $request->address;
        $address->default      = $request->default ? Status::ENABLE : Status::DISABLE;
        $address->save();

        if($request->default) {
            ShippingAddress::where('user_id', auth()->id())->where('default', Status::ENABLE)->where('id', '!=', $address->id)->update(['default' => Status::DISABLE]);
        }

        return back()->withNotify($notify);
    }

    public function delete($id){
        $address = ShippingAddress::where('user_id', auth()->id())->findOrFail($id);
        $address->delete();

        $notify[] = ['success', 'Address deleted succussfully'];
        return back()->withNotify($notify);
    }
}
