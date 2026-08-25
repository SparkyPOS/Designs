<?php
namespace App\Traits;

use App\Constants\Status;

trait VendorNotify
{
    public static function notifyToVendor(){
        return [
            'allVendors'              => 'All Vendors',
            'selectedVendors'         => 'Selected Vendors',
            'kycUnverified'         => 'Kyc Unverified Vendors',
            'kycVerified'           => 'Kyc Verified Vendors',
            'kycPending'            => 'Kyc Pending Vendors',
            'withBalance'           => 'With Balance Vendors',
            'emptyBalanceVendors'     => 'Empty Balance Vendors',
            'twoFaDisableVendors'     => '2FA Disable Vendors',
            'twoFaEnableVendors'      => '2FA Enable Vendors',
            'hasOrderVendors'       => 'Order Vendors',
            'notOrderVendors'       => 'Not Order Vendors',
            'pendingOrderVendors'   => 'Pending Order Vendors',
            'topOrderVendors'     => 'Top Order Vendors',
            'hasWithdrawVendors'      => 'Withdraw Vendors',
            'pendingWithdrawVendors'  => 'Pending Withdraw Vendors',
            'rejectedWithdrawVendors' => 'Rejected Withdraw Vendors',
            'pendingTicketVendor'     => 'Pending Ticket Vendors',
            'answerTicketVendor'      => 'Answer Ticket Vendors',
            'closedTicketVendor'      => 'Closed Ticket Vendors',
            'notLoginVendors'         => 'Last Few Days Not Login Vendors',
        ];
    }

    public function scopeSelectedVendors($query)
    {
        return $query->whereIn('id', request()->vendor ?? []);
    }

    public function scopeAllVendors($query)
    {
        return $query;
    }

    public function scopeEmptyBalanceVendors($query)
    {
        return $query->where('balance', '<=', 0);
    }

    public function scopeTwoFaDisableVendors($query)
    {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableVendors($query)
    {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasOrderVendors($query)
    {
        return $query->whereHas('orders', function ($deposit) {
            $deposit->paid();
        });
    }

    public function scopeNotOrderVendors($query)
    {
        return $query->whereDoesntHave('orders', function ($q) {
            $q->paid();
        });
    }

    public function scopePendingOrderVendors($query)
    {
        return $query->whereHas('orders', function ($deposit) {
            $deposit->pending();
        });
    }


    public function scopeTopOrderVendors($query)
    {
        return $query->whereHas('orders', function ($deposit) {
            $deposit->paid();
        })->withSum(['orders'=>function($q){
            $q->paid();
        }], 'total_amount')->orderBy('orders_sum_amount', 'desc')->take(request()->number_of_top_deposited_vendor ?? 10);
    }

    public function scopeHasWithdrawVendors($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->approved();
        });
    }

    public function scopePendingWithdrawVendors($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->pending();
        });
    }

    public function scopeRejectedWithdrawVendors($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->rejected();
        });
    }

    public function scopePendingTicketVendor($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY]);
        });
    }

    public function scopeClosedTicketVendor($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_CLOSE);
        });
    }

    public function scopeAnswerTicketVendor($query)
    {
        return $query->whereHas('tickets', function ($q) {

            $q->where('status', Status::TICKET_ANSWER);
        });
    }

    public function scopeNotLoginVendors($query)
    {
        return $query->whereDoesntHave('loginLogs', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(request()->number_of_days ?? 10));
        });
    }

    public function scopeKycVerified($query)
    {
        return $query->where('kv', Status::KYC_VERIFIED);
    }

}
