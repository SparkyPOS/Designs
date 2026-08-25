<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductReview;
use App\Http\Controllers\Controller;

class ReviewController extends Controller {

    public function index($productId = null) {
        $pageTitle = "Reviews";
        $reviews = ProductReview::searchable('title', 'product:name', 'order:order_number')->when($productId, function ($query) use ($productId) {
            $query->where('product_id', $productId);
        })->latest()->paginate(getPaginate());
        return view('admin.review.index', compact('pageTitle', 'reviews'));
    }


    public function delete($id) {
        $review = ProductReview::findOrFail($id);
        $review->delete();

        $notify[] = ['success', "Review deleted successfully"];
        return back()->withNotify($notify);
    }
}
