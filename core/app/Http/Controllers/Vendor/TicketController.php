<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Traits\SupportTicketManager;

class TicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        parent::__construct();
        $this->layout = 'frontend';
        $this->redirectLink = 'vendor.ticket.view';
        $this->userType     = 'vendor';
        $this->column       = 'vendor_id';
        $this->user = authVendor();
        $this->layout = 'master';
    }
}
