<?php
namespace App\Traits;

use App\Constants\Status;

trait UserNotify
{
    public static function notifyToUser(){
        return [
            'allUsers'              => 'All Customers',
            'selectedUsers'         => 'Selected Customers',
            'twoFaDisableUsers'     => '2FA Disable Customer',
            'twoFaEnableUsers'      => '2FA Enable Customer',
            'hasOrderUsers'       => 'Order Customers',
            'notOrdersUsers'       => 'Not Order Customers',
            'pendingDepositedUsers'   => 'Pending Payment Customers',
            'rejectedDepositedUsers'  => 'Rejected Payment Customers',
            'topOrderUsers'     => 'Top Order Customers',
            'pendingTicketUser'     => 'Pending Ticket Customers',
            'answerTicketUser'      => 'Answer Ticket Customers',
            'closedTicketUser'      => 'Closed Ticket Customers',
            'notLoginUsers'         => 'Last Few Days Not Login Customers',
        ];
    }

    public function scopeSelectedUsers($query)
    {
        return $query->whereIn('id', request()->user ?? []);
    }

    public function scopeAllUsers($query)
    {
        return $query;
    }

    public function scopeTwoFaDisableUsers($query)
    {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableUsers($query)
    {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasOrderUsers($query)
    {
        return $query->whereHas('orders', function ($deposit) {
            $deposit->paid();
        });
    }

    public function scopeNotOrdersUsers($query)
    {
        return $query->whereDoesntHave('orders', function ($q) {
            $q->paid();
        });
    }

    public function scopePendingDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }

    public function scopeTopOrderUsers($query)
    {
        return $query->whereHas('orders', function ($deposit) {
            $deposit->paid();
        })->withSum(['orders'=>function($q){
            $q->paid();
        }], 'subtotal')->orderBy('orders_sum_subtotal', 'desc')->take(request()->number_of_top_order_user ?? 10);
    }

    public function scopePendingTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY]);
        });
    }

    public function scopeClosedTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_CLOSE);
        });
    }

    public function scopeAnswerTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {

            $q->where('status', Status::TICKET_ANSWER);
        });
    }

    public function scopeNotLoginUsers($query)
    {
        return $query->whereDoesntHave('loginLogs', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(request()->number_of_days ?? 10));
        });
    }

}
