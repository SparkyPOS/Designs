<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Constants\Status;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function ordered()
    {
        $pageTitle = "All Orders";
        $orders    = $this->orderData();
        return view('admin.order.all', compact('pageTitle', 'orders'));
    }

    public function codOrders()
    {
        $pageTitle = "COD Orders";
        $orders    = $this->orderData('cod');
        return view('admin.order.all', compact('pageTitle', 'orders'));
    }

    public function pending()
    {
        $pageTitle = "Pending Orders";
        $orders    = $this->orderData('pending');
        return view('admin.order.all', compact('pageTitle', 'orders'));
    }

    public function onProcessing()
    {
        $pageTitle = "Orders on Processing";
        $orders    = $this->orderData('processing');
        return view('admin.order.all', compact('pageTitle', 'orders'));
    }

    public function dispatched()
    {
        $pageTitle = "Orders Dispatched";
        $orders    = $this->orderData('dispatched');
        return view('admin.order.all', compact('pageTitle', 'orders'));
    }

    public function canceledOrders()
    {
        $pageTitle = "Canceled Orders";
        $orders    = $this->orderData('canceled');
        return view('admin.order.all', compact('pageTitle', 'orders'));
    }

    public function deliveredOrders()
    {
        $pageTitle = "Delivered Orders";
        $orders    = $this->orderData('delivered');
        return view('admin.order.all', compact('pageTitle', 'orders'));
    }

    private function orderData($scope = null)
    {
        $orders = Order::query();

        if($scope) {
            $orders->$scope();
        }
        if(request()->user_id) {
            $orders->where('user_id', request()->user_id);
        }
        if(request()->vendor_id) {
            $orders->where('vendor_id', request()->vendor_id);
        }
        return $orders->searchable(['order_number', 'user:username', 'vendor:username'], false)
            ->with([
                'user',
                'deposit',
                'deposit.gateway'
            ])
            ->orderBy('id', 'DESC')
            ->paginate(getPaginate());
    }

    public function orderDetails($id)
    {
        $order     = Order::where('id', $id)->with('user', 'deposit', 'deposit.gateway', 'orderDetail.product', 'orderDetail.productVariant')->firstOrFail();
        $pageTitle = 'Order Details of ' . $order->order_number;

        return view('admin.order.detail', compact('order', 'pageTitle'));
    }

    public function paymentReturned(Request $request, $orderId) {
        $order = Order::with('vendor')->where('status', Status::ORDER_CANCELED)->findOrFail($orderId);
        $order->status = Status::ORDER_PAYMENT_RETURNED;
        $order->save();

        $userNotification          = new UserNotification();
        $userNotification->user_id = $order->user_id;
        $userNotification->vendor_id = $order->vendor_id;
        $userNotification->title     = "Your order #{$order->order_number} payment has been returned.";;
        $userNotification->click_url = urlPath('user.order.details', $order->order_number);
        $userNotification->save();

        $notify[] = ['success', 'Order payment returned updated successfully.'];
        return back()->withNotify($notify);
    }
}
