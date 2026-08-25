<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Order;
use App\Constants\Status;
use App\Models\OrderDetail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\UserNotification;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;

class OrderController extends Controller {

    protected $pageTitle;

    function allOrders() {
        $this->pageTitle = 'All Orders';
        return $this->orders();
    }

    protected function orders($scope = null) {
        $orders = Order::searchable(['order_number'])->where('vendor_id', authVendorId());

        if ($scope) {
            $orders->$scope();
        }

        $orders    = $orders->with('orderDetail')->orderBy('id', 'desc')->paginate(getPaginate());
        $pageTitle = $this->pageTitle;
        return view('Template::vendor.orders.index', compact('pageTitle', 'orders'));
    }

    function pendingOrders() {
        $this->pageTitle = 'Pending Orders';
        return $this->orders('pending');
    }

    function processingOrders() {
        $this->pageTitle = 'Processing Orders';
        return $this->orders('processing');
    }

    function dispatchedOrders() {
        $this->pageTitle = 'Dispatched Orders';
        return $this->orders('dispatched');
    }

    function completedOrders() {
        $this->pageTitle = 'Completed Orders';
        return $this->orders('delivered');
    }

    function canceledOrders() {
        $this->pageTitle = 'Cancelled Orders';
        return $this->orders('canceled');
    }

    public function orderDetails($orderNumber) {
        $pageTitle = 'Order Details';

        $order = Order::where('vendor_id', authVendorid())->where('order_number', $orderNumber)->with('deposit', 'orderDetail.product', 'orderDetail.productVariant')->firstOrFail();

        return view('Template::vendor.orders.details', compact('order', 'pageTitle'));
    }

    public function orderPrintArea($orderNumber, $id) {
        $order        = Order::where('vendor_id', authVendorid())->where('order_number', $orderNumber)->firstOrFail();
        $orderProduct = OrderDetail::where('order_id', $order->id)->with('product', 'printAreas', 'printAreas.productPrintArea')->findOrFail($id);
        $pageTitle    = $orderProduct?->product->name . (($orderProduct->productVariant) ? "(" . $orderProduct->productVariant->name . ")" : "") . " Print Area";
        $printAreas   = $orderProduct->printAreas;
        $colorCode = null;
        if ($orderProduct->product_variant_id) {
            $colorValue = AttributeValue::whereIn('id', $orderProduct->productVariant->attribute_values)
                ->whereHas('attribute', function ($query) {
                    $query->where('type', Status::ATTRIBUTE_TYPE_COLOR);
                })
                ->first();

            $colorCode = $colorValue ? $colorValue->value : NULL;
        }

        return view('Template::vendor.orders.print_area', compact('order', 'orderProduct', 'pageTitle', 'printAreas', 'colorCode'));
    }

    public function changeStatus(Request $request, $id) {
        $order = Order::paid()->where('vendor_id', authVendorId())->findOrFail($id);
        if ($order->status == Status::ORDER_DELIVERED) {
            $notify[] = ['error', 'This order has already been delivered'];
            return back()->withNotify($notify);
        }

        $order->status += 1;
        $order->save();

        if ($order->status == Status::ORDER_PROCESSING) {
            $action = 'Processing';
        } elseif ($order->status == Status::ORDER_DISPATCHED) {
            $action = 'Dispatched';
        } elseif ($order->status == Status::ORDER_DELIVERED) {
            $action = 'Delivered';
        }

        $this->sendOrderMail($order);

        $notify[] = ['success', 'Order status changed to ' . strtolower($action)];
        return back()->withNotify($notify);
    }

    private function sendOrderMail($order) {
        $shortCode = [
            'site_name' => gs('sitename'),
            'order_id'  => $order->order_number,
        ];

        $userNotification          = new UserNotification();

        $userNotification->user_id = $order->user_id;
        $title                     = 'Order #' . $order->order_number;

        if ($order->status == Status::ORDER_PROCESSING) {
            $template = 'ORDER_ON_PROCESSING_CONFIRMATION';
            $title .= ' is processing';
        } elseif ($order->status == Status::ORDER_DISPATCHED) {
            $template = 'ORDER_DISPATCHED_CONFIRMATION';
            $title .= ' has been dispatched';
        } elseif ($order->status == Status::ORDER_DELIVERED) {
            $template = 'ORDER_DELIVERY_CONFIRMATION';
            $title .= ' has been delivered';
        } elseif ($order->status == Status::ORDER_CANCELED) {
            $template = 'ORDER_CANCELLATION_CONFIRMATION';
            $title .= ' has been cancelled';
        }

        $userNotification->title     = $title;
        $userNotification->click_url = urlPath('user.order.details', $order->order_number);
        $userNotification->save();

        notify($order->user, $template, $shortCode);
    }

    public function cancelStatus($id) {
        $order = Order::with('vendor')->paid()->where('vendor_id', authVendorId())->findOrFail($id);
        if ($order->status != Status::ORDER_PENDING && $order->status != Status::ORDER_PROCESSING) {
            $notify[] = ['error', 'You can\'t cancel the order'];
            return back()->withNotify($notify);
        }

        $order->status = Status::ORDER_CANCELED;
        $order->save();

        // deduct balance
        $deductAmount = $order->total_amount - $order->commission_amount;

        $vendor = $order->vendor;
        $vendor->balance -= $deductAmount;
        $vendor->save();

        $transaction = new Transaction();
        $transaction->vendor_id = $order->vendor_id;
        $transaction->amount = $deductAmount;
        $transaction->post_balance = $vendor->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '-';
        $transaction->details = 'Canceled order #' . $order->order_number;
        $transaction->remark = 'payment_returned';
        $transaction->trx = getTrx();
        $transaction->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $order->user_id;
        $adminNotification->vendor_id   = $order->vendor_id;
        $adminNotification->title     = 'Canceled order #' . $order->order_number;;
        $adminNotification->click_url = urlPath('vendor.order.details', $order->order_number);
        $adminNotification->save();

        $this->sendOrderMail($order);

        $notify[] = ['success', 'Order status changed to canceled'];
        return back()->withNotify($notify);
    }

    public function printInvoice($order) {
        $pageTitle = 'Print Invoice';
        $order     = Order::paid()->with('orderDetail.product')->findOrFail($order);
        return view('Template::vendor.orders.invoice', compact('pageTitle', 'order'));
    }

}
