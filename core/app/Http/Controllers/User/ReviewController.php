<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use App\Constants\Status;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\VendorNotification;
use App\Http\Controllers\Controller;

class ReviewController extends Controller {

    public function index() {
        $pageTitle = "Reviews";
        $reviews = ProductReview::where('user_id', auth()->id())->searchable(['title', 'product:name', 'order:order_number'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.review.index', compact('pageTitle', 'reviews'));
    }

    public function create($orderId, $productId, $variantId = null) {
        $pageTitle    = "Review";
        $order        = Order::paid()->delivered()->where('user_id', auth()->id())->findOrFail($orderId);
        $orderProduct = OrderDetail::where('order_id', $order->id)->where('product_id', $productId)
            ->when($variantId, function ($query) use ($variantId) {
                $query->where('product_variant_id', $variantId);
            })->with('product')->firstOrFail();

        $review = ProductReview::where('user_id', auth()->id())->where('order_id', $order->id)->where('product_id', $orderProduct->product_id)
            ->when($variantId, function ($query) use ($variantId) {
                $query->where('product_variant_id', $variantId);
            })->first();

        return view('Template::user.review.create', compact('order', 'orderProduct', 'pageTitle', 'review'));
    }

    public function edit($id) {
        $pageTitle    = "Review Edit";
        $review = ProductReview::where('user_id', auth()->id())->findOrFail($id);
        $order = $review->order;
        $orderProduct = $review->order->orderDetail()->with('product', 'productVariant')->where('product_id', $review->product_id)->where('product_variant_id', $review->product_variant_id)->first();
        return view('Template::user.review.create', compact('order', 'orderProduct', 'pageTitle', 'review'));
    }

    public function store(Request $request, $id = null) {
        $request->validate([
            'order_id'           => 'required|numeric|exists:orders,id',
            'product_id'         => 'required|numeric|exists:products,id',
            'product_variant_id' => 'nullable|numeric|exists:product_variants,id',
            'title'              => 'required|string|max:255',
            'review'             => 'required|string|max:2000',
            'rating'             => 'required|numeric|in:1,2,3,4,5',
        ]);

        $user = auth()->user();
        if($id) {
            $review = ProductReview::where('user_id', $user->id)->findOrFail($id);
            $notification       = 'updated';
        } else {
            $order        = Order::paid()->delivered()->where('user_id', auth()->id())->findOrFail($request->order_id);
            OrderDetail::where('order_id', $order->id)->where('product_id', $request->product_id)
                ->when($request, function ($query) use ($request) {
                    $query->where('product_variant_id', $request->product_variant_id);
                })->with('product')->firstOrFail();

            $review = new ProductReview();
            $review->user_id = $user->id;
            $review->order_id = $order->id;
            $review->product_id = $request->product_id;
            $review->product_variant_id = $request->product_variant_id;
            $notification = 'added';
        }

        $review->title    = $request->title;
        $review->rating    = $request->rating;
        $review->review    = $request->review;
        $review->status    = Status::REVIEW_APPROVED;
        $review->save();

        $vendorNotification              = new VendorNotification();
        $vendorNotification->user_id     = $user->id;
        $vendorNotification->vendor_id   = $review->order->vendor_id;
        $vendorNotification->title       =  $id?  "Review updated on order #{$review->order?->order_number}" : "New review on order #{$review->order?->order_number}";
        $vendorNotification->click_url   = urlPath('vendor.reviews', 'search='.$review->order?->order_number);
        $vendorNotification->save();

        $notify[] = ['success', "Review $notification successfully"];
        return back()->withNotify($notify);
    }

    public function delete($id) {
        $review = ProductReview::where('user_id', auth()->id())->findOrFail($id);
        $review->delete();

        $notify[] = ['success', "Review deleted successfully"];
        return back()->withNotify($notify);
    }
}
