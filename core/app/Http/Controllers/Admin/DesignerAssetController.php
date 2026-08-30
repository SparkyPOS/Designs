<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Rules\FileTypeValidate;
use App\Services\ProductPrintAreaService;
use Illuminate\Http\Request;

class DesignerAssetController extends Controller
{
    public function index()
    {
        $pageTitle = 'Product 3D Models';
        $products = Product::with('vendor')
            ->searchable(['name', 'vendor:username'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.designer_assets.index', compact('pageTitle', 'products'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'designer_model' => ['nullable', 'file', new FileTypeValidate(['glb', 'gltf']), 'max:51200'],
            'designer_preview' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp']), 'max:5120'],
            'designer_form' => ['required', 'in:' . Product::DESIGNER_FORM_DTG . ',' . Product::DESIGNER_FORM_ENGRAVE],
            'designer_settings' => ['nullable', 'array'],
            'designer_settings.model_scale' => ['nullable', 'numeric'],
            'designer_settings.rotation_x' => ['nullable', 'numeric'],
            'designer_settings.rotation_y' => ['nullable', 'numeric'],
            'designer_settings.rotation_z' => ['nullable', 'numeric'],
            'designer_settings.offset_x' => ['nullable', 'numeric'],
            'designer_settings.offset_y' => ['nullable', 'numeric'],
            'designer_settings.front_x' => ['nullable', 'numeric'],
            'designer_settings.front_y' => ['nullable', 'numeric'],
            'designer_settings.front_width' => ['nullable', 'numeric'],
            'designer_settings.front_height' => ['nullable', 'numeric'],
            'designer_settings.back_x' => ['nullable', 'numeric'],
            'designer_settings.back_y' => ['nullable', 'numeric'],
            'designer_settings.back_width' => ['nullable', 'numeric'],
            'designer_settings.back_height' => ['nullable', 'numeric'],
            'print_area_image' => ['nullable', 'array'],
            'print_area_image.*' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp']), 'max:10240'],
            'print_area_selected' => ['nullable', 'array'],
            'print_area_selected.front' => ['nullable', 'json'],
            'print_area_selected.back' => ['nullable', 'json'],
        ]);

        if (!$request->hasFile('designer_model') && !$request->hasFile('designer_preview') && !$request->has('designer_settings') && !$request->has('designer_form')) {
            $notify[] = ['error', 'Choose a 3D asset or update its settings'];
            return back()->withNotify($notify);
        }

        if ($request->hasFile('designer_model')) {
            $product->designer_model = fileUploader(
                $request->file('designer_model'),
                base_path('../assets/models/designer'),
                null,
                $product->designer_model
            );
        }

        if ($request->hasFile('designer_preview')) {
            $product->designer_preview = fileUploader(
                $request->file('designer_preview'),
                base_path('../assets/images/designer'),
                null,
                $product->designer_preview
            );
        }

        if ($request->has('designer_settings')) {
            $product->designer_settings = Product::normalizeDesignerSettings($request->input('designer_settings', []));
        }

        $product->designer_form = $request->input('designer_form', Product::DESIGNER_FORM_DTG);

        $product->save();

        try {
            app(ProductPrintAreaService::class)->updateFromRequest($request, $product);
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t update the designer canvas images'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Designer assets updated successfully'];
        return back()->withNotify($notify);
    }
}
