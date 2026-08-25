<?php

namespace App\Http\Controllers\Vendor;

use App\Constants\Status;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttributeController extends Controller {
    public function index() {
        $pageTitle  = "All Attributes";
        $attributes = Attribute::where('vendor_id', authVendorId())->searchable(['name'])->withCount('attributeValues')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::vendor.attribute.index', compact('pageTitle', 'attributes'));
    }

    public function store(Request $request, $id = 0) {
        $types = Status::ATTRIBUTE_TYPE_TEXT.','.Status::ATTRIBUTE_TYPE_COLOR.','.Status::ATTRIBUTE_TYPE_IMAGE;
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:'.$types,
        ]);

        if ($id == 0) {
            $attributeType            = new Attribute();
            $attributeType->vendor_id = authVendorId();
            $notification = 'Product Attribute created successfully';
        } else {
            $attributeType = Attribute::where('vendor_id', authVendorId())->findOrFail($id);
            $notification  = 'Product Attribute updated successfully';
        }

        $attributeType->name = $request->name;
        $attributeType->type = $request->type;
        $attributeType->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return Attribute::changeStatus($id);
    }
}
