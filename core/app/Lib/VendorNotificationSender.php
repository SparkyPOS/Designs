<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\NotificationTemplate;
use App\Models\Vendor;

/**
 * Class VendorNotificationSender
 *
 * This class handles the sending of notifications to vendors based on specified criteria.
 * It supports multiple notification channels (e.g., email, push notifications) and manages
 * session data to track the notification sending process.
 */
class VendorNotificationSender
{

    private $isSingleNotification = false;
    /**
     * Send notifications to all or selected vendors based on the request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function notificationToAll($request)
    {
        if (!$this->isTemplateEnabled($request->via)) {
            return $this->redirectWithNotify('warning', 'Default notification template is not enabled');
        }

        $handleSelectedVendor = $this->handleSelectedVendors($request);
        if (!$handleSelectedVendor) {
            return $this->redirectWithNotify('error', "Ensure that the vendor field is populated when sending an email to the designated vendor group");
        }

        $vendorQuery      = $this->getVendorQuery($request);
        $totalVendorCount = $this->getTotalVendorCount($vendorQuery, $request);

        if ($totalVendorCount <= 0) {
            return $this->redirectWithNotify('error', "Notification recipients were not found among the selected vendor base.");
        }

        $imageUrl = $this->handlePushNotificationImage($request);
        $vendors    = $this->getVendors($vendorQuery, $request->start, $request->batch);

        $this->sendNotifications($vendors, $request, $imageUrl);

        return $this->manageSessionForNotification($totalVendorCount, $request);
    }

    /**
     * Send a notification to a single vendor.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $vendorId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function notificationToSingle($request, $vendorId)
    {
        if (!$this->isTemplateEnabled($request->via)) {
            return $this->redirectWithNotify('warning', 'Default notification template is not enabled');
        }
        $this->isSingleNotification = true;
        $imageUrl = $this->handlePushNotificationImage($request);
        $vendor     = Vendor::findOrFail($vendorId);
        $this->sendNotifications($vendor, $request, $imageUrl, true);

        return $this->redirectWithNotify("success", "Notification sent successfully");
    }
    /**
     * Check if the notification template is enabled for the specified channel.
     *
     * @param string $via
     * @return bool
     */
    private function isTemplateEnabled($via)
    {
        return NotificationTemplate::where('act', 'DEFAULT')->where($via . '_status', Status::ENABLE)->exists();
    }

    /**
     * Redirect with a notification message.
     *
     * @param string $type
     * @param string $message
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectWithNotify($type, $message)
    {
        $notify[] = [$type, $message];
        return back()->withNotify($notify);
    }

    /**
     * Handle selected vendors logic, merging vendor data from session if necessary.
     *
     * @param \Illuminate\Http\Request $request
     * @return bol
     */
    private function handleSelectedVendors($request)
    {
        if ($request->being_sent_to == 'selectedVendors') {
            if (session()->has("SEND_NOTIFICATION")) {
                $request->merge(['vendor' => session()->get('SEND_NOTIFICATION')['vendor']]);
            } elseif (!$request->vendor || !is_array($request->vendor) || empty($request->vendor)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get vendor query based on the scope.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getVendorQuery($request)
    {
        $scope = $request->being_sent_to;
        return Vendor::oldest()->active()->$scope();
    }

    /**
     * Get the total vendor count for notification.
     *
     * @param \Illuminate\Database\Eloquent\Builder $vendorQuery
     * @param \Illuminate\Http\Request $request
     * @return int
     */
    private function getTotalVendorCount($vendorQuery, $request)
    {
        if (session()->has("SEND_NOTIFICATION")) {
            $totalVendorCount = session('SEND_NOTIFICATION')['total_vendor'];
        } else {
            $totalVendorCount = (clone $vendorQuery)->count() - ($request->start - 1);
        }
        return $totalVendorCount;
    }

    /**
     * Handle image upload for push notifications.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    private function handlePushNotificationImage($request)
    {
        if ($request->via == 'push') {
            if ($request->hasFile('image')) {
                $imageUrl = fileUploader($request->image, getFilePath('push'));
                session()->put('PUSH_IMAGE_URL', $imageUrl);
                return $imageUrl;
            }
            return $this->isSingleNotification ? null : session()->get('PUSH_IMAGE_URL');
        }
        return null;
    }

    /**
     * Get vendors for notification based on pagination.
     *
     * @param \Illuminate\Database\Eloquent\Builder $vendorQuery
     * @param int $start
     * @param int $batch
     * @return \Illuminate\Support\Collection
     */
    private function getVendors($vendorQuery, $start, $batch)
    {
        return (clone $vendorQuery)->skip($start - 1)->limit($batch)->get();
    }

    /**
     * Send notifications to vendors.
     *
     * @param \Illuminate\Support\Collection $vendors
     * @param \Illuminate\Http\Request $request
     * @param string|null $imageUrl
     * @param bol $isSingleNotification
     * @return void
     */
    private function sendNotifications($vendors, $request, $imageUrl, $isSingleNotification = false)
    {
        if (!$isSingleNotification) {
            foreach ($vendors as $vendor) {
                notify($vendor, 'DEFAULT', [
                    'subject' => $request->subject,
                    'message' => $request->message,
                ], [$request->via], pushImage: $imageUrl);
            }
        } else {
            notify($vendors, 'DEFAULT', [
                'subject' => $request->subject,
                'message' => $request->message,
            ], [$request->via], pushImage: $imageUrl);
        }
    }

    /**
     * Manage session data for notification sending process.
     *
     * @param int $totalVendorCount
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    private function manageSessionForNotification($totalVendorCount, $request)
    {
        if (session()->has('SEND_NOTIFICATION')) {
            $sessionData                = session("SEND_NOTIFICATION");
            $sessionData['total_sent'] += $sessionData['batch'];
        } else {
            $sessionData               = $request->except('_token', 'image');
            $sessionData['total_sent'] = $request->batch;
            $sessionData['total_vendor'] = $totalVendorCount;
        }

        $sessionData['start'] = $sessionData['total_sent'] + 1;

        if ($sessionData['total_sent'] >= $totalVendorCount) {
            session()->forget("SEND_NOTIFICATION");
            $message = ucfirst($request->via) . " notifications were sent successfully";
            $url     = route("admin.vendors.notification.all");
        } else {
            session()->put('SEND_NOTIFICATION', $sessionData);
            $message = $sessionData['total_sent'] . " " . $sessionData['via'] . "  notifications were sent successfully";
            $url     = route("admin.vendors.notification.all") . "?email_sent=yes";
        }

        $notify[] = ['success', $message];
        return redirect($url)->withNotify($notify);
    }
}
