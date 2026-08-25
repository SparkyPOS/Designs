<?php

namespace App\Http\Controllers\Admin;

use App\Models\Catalog;
use App\Constants\Status;
use App\Lib\RequiredConfig;
use Illuminate\Http\Request;
use App\Models\CatalogCategory;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;

class CatalogController extends Controller
{
    public function catalogCategories()
    {
        $pageTitle = 'Catalog Categories';
        $categories = CatalogCategory::withCount('catalogs')->searchable(['name', 'catalogs:name'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.catalog.categories', compact('pageTitle', 'categories'));
    }

    public function checkCategorySlug(Request $request) {
        $exist = CatalogCategory::where('slug', $request->slug)->where('id', '!=', $request->id)->exists();
        return response()->json([
            'exists' => $exist,
        ]);
    }

    public function storeCatalogCategory(Request $request, $id = null)
    {
        $featureStatus = Status::GENERAL.','.Status::FEATURED.','.Status::MEGA_MENU;
        $request->validate([
            'name'  => 'required|max:255|unique:catalog_categories,name,' . $id . ',id',
            'slug'  => 'required|max:255|unique:catalog_categories,slug,' . $id . ',id',
            'feature' => 'required|in:'.$featureStatus,
            'image' => [$id ? 'nullable' : 'required', new FileTypeValidate(['png', 'jpg', 'jpeg'])]
        ]);

        if ($id) {
            $catalogCategory = CatalogCategory::findOrFail($id);
        } else {
            $catalogCategory = new CatalogCategory();
        }

        $catalogCategory->name = $request->name;
        $catalogCategory->slug = $request->slug;
        $catalogCategory->feature = $request->feature;

        if ($request->hasFile('image')) {
            $catalogCategory->image = fileUploader(
                $request->file('image'),
                getFilePath('catalogCategory'),
                getFileSize('catalogCategory'),
                $id ? $catalogCategory->image : null
            );
        }

        $catalogCategory->save();

        RequiredConfig::configured('catalog_category');
        $notify[] = ['success', 'Catalog category saved successfully'];
        return back()->withNotify($notify);
    }


    public function categorySeo($id)
    {
        $seoInfo = CatalogCategory::findOrFail($id);
        $pageTitle = "SEO settings of ".$seoInfo->name;
        $type = "category";
        return view('admin.catalog.seo', compact('pageTitle', 'seoInfo', 'type'));
    }

    public function categorySeoStore(Request $request, $id)
    {
        $request->validate([
            'meta_keywords'  => 'nullable|array',
            'meta_keywords.*'  => 'nullable|string',
            'meta_robots'  => 'nullable|string',
            'meta_description'  => 'nullable|string',
            'social_title'  => 'nullable|string',
            'social_description'  => 'nullable|string'
        ]);

        $catalogCategory = CatalogCategory::findOrFail($id);
        $catalogCategory->meta_keywords = $request->meta_keywords;
        $catalogCategory->meta_robots = $request->meta_robots;
        $catalogCategory->meta_description = $request->meta_description;
        $catalogCategory->social_title = $request->social_title;
        $catalogCategory->social_description = $request->social_description;
        $catalogCategory->save();

        $notify[] = ['success', 'Catalog category SEO updated successfully'];
        return back()->withNotify($notify);
    }


    public function catalogCategoryStatus($id)
    {
        return CatalogCategory::changeStatus($id);
    }

    public function catalogs()
    {
        $pageTitle = 'Catalogs';
        $catalogs = Catalog::searchable(['name','catalogCategory:name'])->with('catalogCategory')->orderBy('id', 'desc')->paginate(getPaginate());
        $catalogCategories = CatalogCategory::active()->orderBy('name')->get();

        return view('admin.catalog.catalogs', compact('pageTitle', 'catalogs', 'catalogCategories'));
    }

    public function checkCatalogSlug(Request $request) {
        $exist = Catalog::where('slug', $request->slug)->where('catalog_category_id', $request->catalog_category_id)->where('id', '!=', $request->id)->exists();

        return response()->json([
            'exists' => $exist,
        ]);
    }


    public function storeCatalog(Request $request, $id = null)
    {
        $request->validate([
            'name' => [
                'required',
                'max:255',
                'unique:catalogs,name,' . $id . ',id,catalog_category_id,' . $request->catalog_category_id
            ],
            'slug'                => 'required|max:255|unique:catalogs,slug,' . $id . ',id,catalog_category_id,' . $request->catalog_category_id,
            'catalog_category_id' => 'required|exists:catalog_categories,id',
            'image'               => [$id ? 'nullable' : 'required', new FileTypeValidate(['png', 'jpg', 'jpeg'])]
        ]);

        if ($id) {
            $catalog = Catalog::findOrFail($id);
        } else {
            $catalog = new Catalog();
        }

        $catalog->catalog_category_id = $request->catalog_category_id;
        $catalog->name                = $request->name;
        $catalog->slug                = $request->slug;

        if ($request->hasFile('image')) {
            $catalog->image = fileUploader(
                $request->file('image'),
                getFilePath('catalog'),
                getFileSize('catalog'),
                $id ? $catalog->image : null
            );
        }

        $catalog->save();

        RequiredConfig::configured('catalog');
        $notify[] = ['success', 'Catalog saved successfully'];
        return back()->withNotify($notify);
    }

    public function catalogStatus($id)
    {
        return Catalog::changeStatus($id);
    }


    public function catalogSeo($id)
    {
        $seoInfo = Catalog::findOrFail($id);
        $pageTitle = "SEO settings of ".$seoInfo->name;
        $type = "catalog";
        return view('admin.catalog.seo', compact('pageTitle', 'seoInfo', 'type'));
    }

    public function catalogSeoStore(Request $request, $id)
    {
        $request->validate([
            'meta_keywords'  => 'nullable|array',
            'meta_keywords.*'  => 'nullable|string',
            'meta_robots'  => 'nullable|string',
            'meta_description'  => 'nullable|string',
            'social_title'  => 'nullable|string',
            'social_description'  => 'nullable|string'
        ]);

        $catalogCategory = Catalog::findOrFail($id);
        $catalogCategory->meta_keywords = $request->meta_keywords;
        $catalogCategory->meta_robots = $request->meta_robots;
        $catalogCategory->meta_description = $request->meta_description;
        $catalogCategory->social_title = $request->social_title;
        $catalogCategory->social_description = $request->social_description;
        $catalogCategory->save();

        $notify[] = ['success', 'Catalog SEO updated successfully'];
        return back()->withNotify($notify);
    }
}
