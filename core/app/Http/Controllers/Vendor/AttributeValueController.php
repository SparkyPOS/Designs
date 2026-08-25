<?php

namespace App\Http\Controllers\Vendor;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class AttributeValueController extends Controller {
    public function index($id) {
        $attribute  = Attribute::where('vendor_id', authVendorId())->findOrFail($id);
        $pageTitle  = "Values of " . $attribute->name;
        $attributeValues = $attribute->attributeValues()->searchable(['name', 'value'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::vendor.attribute.values', compact('pageTitle', 'attribute', 'attributeValues'));
    }

    function store(Request $request, $id) {
        $attribute = Attribute::where('vendor_id', authVendorId())->findOrFail($id);

        $request->validate([
            "value_id" => "nullable|exists:attribute_values,id",
            'name'  => 'required|string',
            'value' => $this->validationRules($attribute->type),
        ]);

        if($request->value_id) {
            $attributeValue = AttributeValue::findOrFail($request->value_id);
            $notify[] = ['success', 'Attibute value updated successfully'];
        } else {
            $attributeValue = new AttributeValue();
            $notify[] = ['success', 'New attribute value added successfully'];
        }


        if ($attribute->type == Status::ATTRIBUTE_TYPE_IMAGE) {
            $attributeValue->value = $this->uploadAttributeImage($request, $attributeValue->value ?? null);
        } else {
            $attributeValue->value = $request->value;
        }

        $attributeValue->attribute_id = $id;
        $attributeValue->name         = $request->name;
        $attributeValue->save();
        return back()->withNotify($notify);
    }

    private function validationRules($type) {
        if ($type == Status::ATTRIBUTE_TYPE_IMAGE) {
            return ['nullable', 'required_if:value_id,null', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
        } elseif ($type == Status::ATTRIBUTE_TYPE_COLOR) {
            return 'required|regex:/^[a-f0-9]{6}$/i';
        } else {
            return 'required|string';
        }
    }

    private function uploadAttributeImage($request, $oldValue) {
        if (is_file($request->value)) {
            return fileUploader($request->value, getFilePath('attribute'), getFileSize('attribute'), $oldValue);
        }
        return $oldValue;
    }

}
