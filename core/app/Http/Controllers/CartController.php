<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Constants\Status;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\CartPrintArea;
use App\Models\OrderPrintArea;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request) {
        $validator = Validator::make($request->all(), [
            'product_id'      => 'required|exists:products,id',
            'variant_id'      => 'nullable|exists:product_variants,id',
            'quantity'        => 'required|integer|min:0',
            "print_area_id"   => 'nullable|array',
            "print_area_id.*" => 'required|exists:product_print_areas,id',
            'selected_area'    => 'nullable|array',
            'selected_area.*'  => 'required|json',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $product = Product::with('productVariants')->published()->find($request->product_id);

        if ($product->usesDesigner() && (blank($request->print_area_id) || blank($request->selected_area))) {
            return responseError('validation_error', 'A design is required for this product');
        }

        if ($product->product_type == Status::PRODUCT_TYPE_VARIABLE && $request->variant_id == NULL) {
            return responseError('validation_error', 'Product not found');
        }

        if($this->isInvalidVendor($product->vendor_id)) {
            $notify[] = ['error', 'You already have items from another vendor in your cart. Please complete that order before adding new items.'];
            return responseError('validation_error', $notify);
        }

        $this->productAddToCart($request, $product);

        return responseSuccess('prduct_add_to_cart', 'Added to cart', ['totalProduct' => count(getCartData())]);

    }

    public function buyNow(Request $request) {
        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'variant_id'      => 'nullable|exists:product_variants,id',
            'quantity'        => 'required|integer|min:0',
            "print_area_id"   => 'nullable|array',
            "print_area_id.*" => 'required|exists:product_print_areas,id',
            'selected_area'    => 'nullable|array',
            'selected_area.*'  => 'required|json',
        ]);

        $product = Product::with('productVariants')->published()->find($request->product_id);

        if ($product->usesDesigner() && (blank($request->print_area_id) || blank($request->selected_area))) {
            $notify[] = ['error', 'A design is required for this product'];
            return back()->withNotify($notify);
        }

        if ($product->product_type == Status::PRODUCT_TYPE_VARIABLE && $request->variant_id == NULL) {
            $notify[] = ['error', 'Product not found'];
            return back()->withNotify($notify);
        }

        if($this->isInvalidVendor($product->vendor_id)) {
            $notify[] = ['error', 'You already have items from another vendor in your cart. Please complete that order before adding new items.'];
            return back()->withNotify($notify);
        }

        $this->productAddToCart($request, $product);
        return to_route('product.checkout');
    }

    private function productAddToCart($request, $product) {
        $cartProductQuery = Cart::where('product_id', $request->product_id);
        if (auth()->check()) {
            $cartProductQuery->where('user_id', auth()->id());
        } else {
            $cartProductQuery->where('session_id', getSessionId());
        }
        if ($request->variant_id) {
            $cartProductQuery->where('product_variant_id', $request->variant_id);
        }

        $cartProduct = $cartProductQuery->first();
        if (!$cartProduct) {
            $cartProduct                     = new Cart();
            $cartProduct->user_id            = auth()->id() ?? NULL;
            $cartProduct->session_id         = getSessionId();
            $cartProduct->product_id         = $product->id;
            $cartProduct->vendor_id          = $product->vendor_id;
            $cartProduct->product_variant_id = $request->variant_id;
        }
        $cartProduct->quantity = $request->quantity;

        $cartProduct->save();

        foreach ($request->print_area_id ?? [] as $key => $printAreaId) {
            $printArea = CartPrintArea::where('cart_id', $cartProduct->id)->where('product_print_area_id', $printAreaId)->first();
            if (!$printArea) {
                $printArea                        = new CartPrintArea();
                $printArea->cart_id               = $cartProduct->id;
                $printArea->product_print_area_id = $printAreaId;
            }

            $printArea->selected_area_design = $this->normalizeDesignerArtwork($request->selected_area[$key], $product);
            $printArea->save();
        }
    }

    private function normalizeDesignerArtwork(string $design, Product $product): string
    {
        if (!$product->usesEngraveDesigner()) {
            return $design;
        }

        $decoded = json_decode($design, true);
        if (!is_array($decoded)) {
            return $design;
        }

        $decoded['objects'] = array_map(
            fn ($object) => is_array($object) ? $this->normalizeEngraveObject($object) : $object,
            $decoded['objects'] ?? []
        );

        return json_encode($decoded, JSON_UNESCAPED_SLASHES);
    }

    private function normalizeEngraveObject(array $object): array
    {
        if (strtolower($object['type'] ?? '') === 'image') {
            $object['filters'] = [[
                'type' => 'Grayscale',
                'mode' => 'luminosity',
            ]];
        } else {
            if (array_key_exists('fill', $object) && !in_array($object['fill'], [null, 'transparent'], true)) {
                $object['fill'] = '#000000';
            }
            if (array_key_exists('stroke', $object) && !in_array($object['stroke'], [null, 'transparent'], true)) {
                $object['stroke'] = '#000000';
            }
            if (!empty($object['shadow'])) {
                $object['shadow'] = null;
            }
        }

        if (!empty($object['objects']) && is_array($object['objects'])) {
            $object['objects'] = array_map(
                fn ($child) => is_array($child) ? $this->normalizeEngraveObject($child) : $child,
                $object['objects']
            );
        }

        return $object;
    }

    private function isInvalidVendor($vendorId) {
        $queryProduct = Cart::query();
        if (auth()->check()) {
            $queryProduct->where('user_id', auth()->id());
        } else {
            $queryProduct->where('session_id', getSessionId());
        }
        $product = $queryProduct->first();
        if($product && $product->vendor_id != $vendorId) {
            return true;
        }
        return false;
    }

    public function checkout() {
        $pageTitle       = 'Checkout';
        $cartProducts = getCartData();
        $addresseses     = ShippingAddress::where('user_id', auth()->id())->get();
        return view('Template::checkout', compact('pageTitle', 'cartProducts', 'addresseses'));
    }

    public function updateCart(Request $request) {
        $validator = Validator::make($request->all(), [
            'cart_id'  => 'required|exists:carts,id',
            'quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $queryProduct = Cart::query();
        if (auth()->check()) {
            $queryProduct->where('user_id', auth()->id());
        } else {
            $queryProduct->where('session_id', getSessionId());
        }
        $productCart = $queryProduct->find($request->cart_id);

        if (!$productCart) {
            $notify[] = ['error', 'Product not found'];
            return responseError('validation_error', $notify);
        }

        $productCart->quantity = $request->quantity;
        $productCart->save();

        $cartProducts = getCartData();
        $addresseses     = ShippingAddress::where('user_id', auth()->id())->get();
        $html            = view('Template::partials.cart_products', compact('cartProducts', 'addresseses'))->render();
        return responseSuccess('prduct_add_to_cart', 'Cart updated', ['html' => $html]);
    }

    public function deleteCart(Request $request) {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|exists:carts,id',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $queryProduct = Cart::with('cartPrintAreas');
        if (auth()->check()) {
            $queryProduct->where('user_id', auth()->id());
        } else {
            $queryProduct->where('session_id', getSessionId());
        }
        $productCart = $queryProduct->find($request->cart_id);

        if (!$productCart) {
            $notify[] = ['error', 'Product not found'];
            return responseError('validation_error', $notify);
        }

        foreach ($productCart->cartPrintAreas as $printArea) {
            $printArea->delete();
        }
        $productCart->delete();

        $cartProducts = getCartData();
        $addresseses     = ShippingAddress::where('user_id', auth()->id())->get();
        $html            = view('Template::partials.cart_products', compact('cartProducts', 'addresseses'))->render();
        return responseSuccess('prduct_add_to_cart', 'Cart updated', ['html' => $html, 'totalProduct' => count(getCartData())]);
    }


    public function order(Request $request) {
        $request->validate([
            'shipping_address_id' => 'required|exists:shipping_addresses,id',
        ]);
        $user            = auth()->user();
        $shippingAddress = ShippingAddress::where('user_id', $user->id)
            ->findOrFail($request->shipping_address_id);
        $cartProducts = getCartData();

        if(blank($cartProducts)) {
            $notify[] = "Your cart is empty";
            return back()->withNotify($notify);
        }

        if(count($cartProducts->groupBy('vendor_id')) > 1) {
            $notify[] = ['error', 'You cannot place orders from multiple vendors at once.'];
            return back()->withNotify($notify);
        }

        $vendor = $cartProducts->first()->vendor;
        $shippingFee = (float) ($vendor->shipping_fee ?? 0);
        $order = $this->saveOrder($user->id, $vendor, $shippingAddress, $shippingFee);
        $subtotal = 0;


        foreach ($cartProducts as $cartProduct) {
            $orderProduct = new OrderDetail();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $cartProduct->product_id;
            $orderProduct->product_variant_id = $cartProduct->product_variant_id;
            $orderProduct->quantity = $cartProduct->quantity;
            $orderProduct->price = $cartProduct->product->getSalePrice($cartProduct->product_variant_id ?: NULL);
            $orderProduct->discount = $cartProduct->product->getRegularPrice($cartProduct->product_variant_id ?: NULL) - $orderProduct->price;
            $orderProduct->save();

            $cartPrintAreaData = [];
            foreach($cartProduct->cartPrintAreas as $cartPrintArea) {
                $cartPrintAreaData[] = [
                    'order_detail_id' => $orderProduct->id,
                    'product_print_area_id' => $cartPrintArea->product_print_area_id,
                    'selected_area_design' => $cartPrintArea->selected_area_design,
                    'created_at'           => now()
                ];
            }

            OrderPrintArea::insert($cartPrintAreaData);

            $subtotal += $orderProduct->price * $orderProduct->quantity;
        }

        $order->subtotal = $subtotal;
        $order->shipping_fee = $shippingFee;
        $order->total_amount = $shippingFee + $subtotal;
        $order->commission_amount = $subtotal * (gs('commission_percentage') / 100);
        $order->save();

        deleteCartData();

        return to_route('user.deposit.index', $order->order_number);
    }

    private function saveOrder($userId, $vendor, $shippingAddress, float $shippingFee) {
        $order = new Order();
        $order->order_number = getOrderNumber();
        $order->user_id = $userId;
        $order->vendor_id = $vendor->id;
        $order->shipping_address = $shippingAddress;
        $order->shipping_fee = $shippingFee;
        $order->save();

        return $order;
    }

    public function carts() {
        $pageTitle       = 'Carts';
        $cartProducts = getCartData();
        $addresseses     = ShippingAddress::where('user_id', auth()->id())->get();
        return view('Template::checkout', compact('pageTitle', 'cartProducts', 'addresseses'));
    }

    public function clearCart() {
        deleteCartData();

        return to_route('home');
    }
}
