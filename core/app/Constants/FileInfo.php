<?php

namespace App\Constants;

class FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This class basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
    */

    public function fileInfo()
    {
        $data['withdrawVerify'] = [
            'path' => 'assets/images/verify/withdraw'
        ];
        $data['depositVerify'] = [
            'path'      => 'assets/images/verify/deposit'
        ];
        $data['verify'] = [
            'path'      => 'assets/verify'
        ];
        $data['default'] = [
            'path'      => 'assets/images/default.png',
        ];
        $data['ticket'] = [
            'path'      => 'assets/support',
        ];
        $data['logoIcon'] = [
            'path'      => 'assets/images/logo_icon',
        ];
        $data['favicon'] = [
            'size'      => '128x128',
        ];
        $data['extensions'] = [
            'path'      => 'assets/images/extensions',
            'size'      => '36x36',
        ];
        $data['seo'] = [
            'path'      => 'assets/images/seo',
            'size'      => '1180x600',
        ];
        $data['userProfile'] = [
            'path'      => 'assets/images/user/profile',
            'size'      => '350x300',
        ];
        $data['vendorProfile'] = [
            'path'      => 'assets/images/vendor/profile',
            'size'      => '500x500',
        ];
        $data['adminProfile'] = [
            'path'      => 'assets/admin/images/profile',
            'size'      => '400x400',
        ];
        $data['push'] = [
            'path'      => 'assets/images/push_notification',
        ];
        $data['maintenance'] = [
            'path'      => 'assets/images/maintenance',
            'size'      => '660x325',
        ];
        $data['language'] = [
            'path' => 'assets/images/language',
            'size' => '50x50'
        ];
        $data['gateway'] = [
            'path' => 'assets/images/gateway',
            'size' => ''
        ];
        $data['withdrawMethod'] = [
            'path' => 'assets/images/withdraw_method',
            'size' => ''
        ];
        $data['pushConfig'] = [
            'path'      => 'assets/admin',
        ];
        $data['catalogCategory'] = [
            'path' => 'assets/images/catalog_category/',
            'size' => '610x470'
        ];
        $data['catalog'] = [
            'path' => 'assets/images/catalog',
            'size' => '610x470'
        ];
        $data['attribute'] = [
            'path' => 'assets/images/attribute_values',
            'size' => '64x64',
        ];
        $data['product'] = [
            'path' => 'assets/images/product',
            'size' => '1270x1200',
            'thumb' => '635x600',
        ];
        $data['printArea'] = [
            'path' => 'assets/images/print_area'
        ];

        return $data;
    }
}
