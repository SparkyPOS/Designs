<?php

namespace App\Models;

use App\Constants\Status;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $casts = [
        'shipping_address' => 'object'
    ];


    public function user() {
        return $this->belongsTo(User::class);
    }

    public function deposit() {
        return $this->hasOne(Deposit::class)->latest()->withDefault();
    }

    public function vendor() {
        return $this->belongsTo(Vendor::class);
    }

    public function products() {
        return $this->belongsToMany(Product::class, 'order_details');
    }


    public function orderDetail() {
        return $this->hasMany(OrderDetail::class);
    }

    public function totalPendingOrder() {
        return $this->hasMany(OrderDetail::class)->whereHas('order', function ($query) {
            $query->pending();
        })->count();
    }

    public function totalCompletedOrder() {
        return $this->hasMany(OrderDetail::class)->whereHas('order', function ($query) {
            $query->compelted();
        })->count();
    }

    public function getAmountAttribute() {
        return $this->total_amount - $this->shipping_charge;
    }

    public function scopePending($query) {
        return $query->where('status', Status::ORDER_PENDING);
    }

    public function scopeProcessing($query) {
        return $query->where('status', Status::ORDER_PROCESSING);
    }

    public function scopeDispatched($query) {
        return $query->where('status', Status::ORDER_DISPATCHED);
    }

    public function scopeDelivered($query) {
        return $query->where('status', Status::ORDER_DELIVERED);
    }

    public function scopeCanceled($query) {
        return $query->whereIn('status', [Status::ORDER_CANCELED, Status::ORDER_PAYMENT_RETURNED]);
    }

    public function scopePaid($query) {
        return $query->where('payment_status', Status::YES);
    }

    public function scopeNonPaid($query) {
        return $query->where('payment_status', Status::DISABLE);
    }

    public function statusBadge() {
        if ($this->status == Status::ORDER_PENDING) {
            $class = 'warning';
            $text  = 'Pending';
        } elseif ($this->status == Status::ORDER_PROCESSING) {
            $class = 'primary';
            $text  = 'Processing';
        } elseif ($this->status == Status::ORDER_DISPATCHED) {
            $class = 'dark';
            $text  = 'Dispatched';
        } elseif ($this->status == Status::ORDER_DELIVERED) {
            $class = 'success';
            $text  = 'Delivered';
        } elseif ($this->status == Status::ORDER_CANCELED || $this->status == Status::ORDER_PAYMENT_RETURNED) {
            $class = 'danger';
            $text  = 'Cancelled';
        }
        return "<span class='badge custom--badge badge--$class'>" . trans($text) . "</span>";
    }

    public function paymentBadge() {
        if ($this->payment_status == Status::PAYMENT_SUCCESS) {
            if ($this->status == Status::ORDER_PAYMENT_RETURNED) {
                return '<span class="badge custom--badge badge--warning">' . trans('Payment Returned') . '</span>';
            } else {
                return '<span class="badge custom--badge badge--success">' . trans('Paid') . '</span>';
            }
        } else {
            return '<span class="badge custom--badge badge--danger">' . trans('Not Paid') . '</span>';
        }
    }
}
