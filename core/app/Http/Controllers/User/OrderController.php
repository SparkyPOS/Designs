<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use App\Constants\Status;
use App\Models\OrderDetail;
use App\Models\AttributeValue;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    protected $pageTitle;

    function allOrders()
    {
        $this->pageTitle = 'All Orders';
        return $this->orders();
    }

    protected function orders($scope = null)
    {
        $orders = Order::searchable(['order_number'])->where('user_id', auth()->id());

        if ($scope) {
            $orders->$scope();
        }

        $orders = $orders->with('orderDetail')->orderBy('id', 'desc')->paginate(getPaginate());
        $pageTitle = $this->pageTitle;
        return view('Template::user.orders.index', compact('pageTitle', 'orders'));
    }


    function pendingOrders()
    {
        $this->pageTitle = 'Pending Orders';
        return $this->orders('pending');
    }

    function processingOrders()
    {
        $this->pageTitle = 'Processing Orders';
        return $this->orders('processing');
    }

    function dispatchedOrders()
    {
        $this->pageTitle = 'Dispatched Orders';
        return $this->orders('dispatched');
    }

    function completedOrders()
    {
        $this->pageTitle = 'Completed Orders';
        return $this->orders('delivered');
    }

    function canceledOrders()
    {
        $this->pageTitle = 'Cancelled Orders';
        return $this->orders('canceled');
    }

    public function orderDetails($orderNumber)
    {
        $pageTitle = 'Order Details';
        $order = Order::where('user_id', auth()->id())->where('order_number', $orderNumber)->with('deposit', 'orderDetail.product', 'orderDetail.productVariant', 'orderDetail.printAreas')->firstOrFail();
        return view('Template::user.orders.details', compact('order', 'pageTitle'));
    }

    public function orderPrintArea($orderNumber, $id)
    {
        $order = Order::where('user_id', auth()->id())->where('order_number', $orderNumber)->firstOrFail();
        $orderProduct = OrderDetail::where('order_id', $order->id)->with('product', 'printAreas', 'printAreas.productPrintArea')->findOrFail($id);
        $pageTitle = $orderProduct?->product->name . (($orderProduct->productVariant) ? "(" . $orderProduct->productVariant->name . ")" : "") . " Print Area";
        $printAreas = $orderProduct->printAreas;

        $colorCode = null;
        if ($orderProduct->product_variant_id) {
            $colorValue = AttributeValue::whereIn('id', $orderProduct->productVariant->attribute_values)
                ->whereHas('attribute', function ($query) {
                    $query->where('type', Status::ATTRIBUTE_TYPE_COLOR);
                })
                ->first();

            $colorCode = $colorValue ? $colorValue->value : NULL;
        }

        return view('Template::user.orders.print_area', compact('order', 'orderProduct', 'pageTitle', 'printAreas', 'colorCode'));
    }

    public function orderConfirm($orderNumber)
    {
        $order = Order::where('user_id', auth()->id())->where('order_number', $orderNumber)->firstOrFail();
        $pageTitle = 'Order Confirm';
        return view('Template::user.orders.confirm', compact('order', 'pageTitle'));
    }
}
