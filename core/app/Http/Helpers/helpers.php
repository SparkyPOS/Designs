<?php

use Carbon\Carbon;
use App\Lib\Captcha;
use App\Models\Cart;
use App\Models\Order;
use App\Notify\Notify;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\Frontend;
use App\Models\Language;
use App\Constants\Status;
use App\Models\Extension;
use Illuminate\Support\Str;
use App\Models\GeneralSetting;
use Laramin\Utility\VugiChugi;
use App\Lib\GoogleAuthenticator;
use Illuminate\Support\Facades\Cache;

function systemDetails() {
    $system['name'] = 'viserpod';
    $system['version'] = '1.0';
    $system['build_version'] = '5.1.16';
    return $system;
}

function slug($string) {
    return Str::slug($string);
}

function verificationCode($length) {
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8) {
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function activeTemplate($asset = false) {
    $template = session('template') ?? gs('active_template');
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName() {
    $template = session('template') ?? gs('active_template');
    return $template;
}

function siteLogo($type = null) {
    $name = $type ? "/logo_$type.png" : '/logo.png';
    return getImage(getFilePath('logoIcon') . $name);
}

function siteFavicon() {
    return getImage(getFilePath('logoIcon') . '/favicon.png');
}

function loadReCaptcha() {
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003') {
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha() {
    return Captcha::verify();
}

function loadExtension($key) {
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2) {
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false, $currencyFormat = true) {
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    if ($currencyFormat) {
        if (gs('currency_format') == Status::CUR_BOTH) {
            return gs('cur_sym') . $printAmount . ' ' . __(gs('cur_text'));
        } elseif (gs('currency_format') == Status::CUR_TEXT) {
            return $printAmount . ' ' . __(gs('cur_text'));
        } else {
            return gs('cur_sym') . $printAmount;
        }
    }
    return $printAmount;
}


function removeElement($array, $value) {
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet) {
    return "https://api.qrserver.com/v1/create-qr-code/?data=$wallet&size=300x300&ecc=m";
}

function keyToTitle($text) {
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text) {
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10) {
    return Str::limit($title, $length);
}


function getIpInfo() {
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser() {
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates() {
    $param['purchasecode'] = env("PURCHASECODE");

    $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $param['website'] = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '' . $requestUri . ' - ' . env("APP_URL");
    $url = VugiChugi::gttmp() . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false) {
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image, $size = null, $avatar = false, $avatarName = "avatar.png") {
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($avatar) {
        return asset('assets/images/' . $avatarName);
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}


function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $pushImage = null) {
    $globalShortCodes = [
        'site_name' => gs('site_name'),
        'site_currency' => gs('cur_text'),
        'currency_symbol' => gs('cur_sym'),
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->pushImage = $pushImage;
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = null) {
    if (!$paginate) {
        $paginate = gs('paginate_number');
    }
    return $paginate;
}

function paginateLinks($data, $view = null) {
    return $data->appends(request()->all())->links($view);
}


function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3) $class = 'side-menu--open';
    elseif ($type == 2) $class = 'sidebar-submenu__open';
    else $class = 'active';
    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        $routeParam = array_values(isset(request()->route()->parameters) ? request()->route()->parameters : []);
        $firstParam = $routeParam[0] ?? null;
        if (is_string($firstParam) && is_string($param) && strcasecmp($firstParam, $param) === 0) {
            return $class;
        }
        return $class;
    }
}

function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null) {
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager() {
    return new FileManager();
}

function getFilePath($key) {
    return fileManager()->$key()->path;
}

function getFileSize($key) {
    return fileManager()->$key()->size;
}

function getThumbSize($key) {
    return fileManager()->$key()->thumb;
}

function getFileExt($key) {
    return fileManager()->$key()->extensions;
}

function diffForHumans($date) {
    $lang = session()->get('lang');
    if (!$lang) {
        $lang = getDefaultLang();
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'Y-m-d h:i A') {
    if (!$date) {
        return '-';
    }
    $lang = session()->get('lang');
    if (!$lang) {
        $lang = getDefaultLang();
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function getDefaultLang() {
    return Language::where('is_default', Status::YES)->first()->code ?? 'en';
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false) {

    $templateName = activeTemplateName();
    if ($singleQuery) {
        $content = Frontend::where('tempname', $templateName)->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::where('tempname', $templateName);
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}

function verifyG2fa($user, $code, $secret = null) {
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = Status::YES;
        $user->save();
        return true;
    } else {
        return false;
    }
}

function urlPath($routeName, $routeParam = null) {
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number) {
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email) {
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP() {
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(isset($_SERVER['HTTP_FORWARDED']) ? $_SERVER['HTTP_FORWARDED'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(isset($_SERVER['HTTP_FORWARDED_FOR']) ? $_SERVER['HTTP_FORWARDED_FOR'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value) {
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b) {
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr) {
    usort($arr, "dateSort");
    return $arr;
}

function gs($key = null) {
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key) return isset($general->$key) ? $general->$key : null;
    return $general;
}
function isImage($string) {
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension = pathinfo($string, PATHINFO_EXTENSION);
    if (in_array($fileExtension, $allowedExtensions)) {
        return true;
    } else {
        return false;
    }
}

function isHtml($string) {
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}


function convertToReadableSize($size) {
    preg_match('/^(\d+)([KMG])$/', $size, $matches);
    $size = (int)$matches[1];
    $unit = $matches[2];

    if ($unit == 'G') {
        return $size . 'GB';
    }

    if ($unit == 'M') {
        return $size . 'MB';
    }

    if ($unit == 'K') {
        return $size . 'KB';
    }

    return $size . $unit;
}


function frontendImage($sectionName, $image, $size = null, $seo = false) {
    if ($seo) {
        return getImage('assets/images/frontend/' . $sectionName . '/seo/' . $image, $size);
    }
    return getImage('assets/images/frontend/' . $sectionName . '/' . $image, $size);
}


function buildResponse($remark, $status, $notify, $data = null) {
    $response = [
        'remark' => $remark,
        'status' => $status,
    ];
    $message = [];
    if ($notify instanceof \Illuminate\Support\MessageBag) {
        $message['error']  = collect($notify)->map(function ($item) {
            return $item[0];
        })->values()->toArray();
    } else {
        $message = [$status => collect($notify)->map(function ($item) {
            if (is_string($item)) {
                return $item;
            }
            if (count($item) > 1) {
                return $item[1];
            }
            return $item[0];
        })->toArray()];
    }
    $response['message'] = $message;
    if ($data) {
        $response['data'] = $data;
    }
    return response()->json($response);
}

function responseSuccess($remark, $notify, $data = null) {
    return buildResponse($remark, 'success', $notify, $data);
}

function responseError($remark, $notify, $data = null) {
    return buildResponse($remark, 'error', $notify, $data);
}

function authVendor() {
    return auth('vendor')->user();
}

function authVendorId() {
    return auth('vendor')->id();
}

function createUniqueSlug($name, $model, $id = 0) {
    $slug = slug($name ?? 'No title');

    $originalSlug = $slug;

    $query = $model::where('slug', $slug);

    if ($id) {
        $query->where('id', '!=', $id);
    }

    $i = 1;

    while ($query->exists()) {
        $slug = $originalSlug . '-' . $i++;
        $query = $model::where('slug', $slug);
    }

    return $slug;
}

function getSeoContents($model) {
    return (object) [
        'social_title'       => $model->meta_title,
        'social_description' => $model->meta_description,
        'meta_robots'        => $model->meta_robots,
        'description'        => $model->meta_description,
        'keywords'           => $model->meta_keywords,
        'image'              => $model->seo_image,
        'image_size'         => $model->seo_image_size,
    ];
}


function productBackRoute($isUpdateOrNew, $currentStep, $slug = NULL, $productType = NULL) {
    if ($isUpdateOrNew == 'new') {
        $route = "vendor.products.create";
    } else {
        $route = "vendor.products.edit";
    }
    if ($currentStep == Status::STEP_GENERAL) {
        return "#";
    } elseif ($currentStep == Status::STEP_DESCRIPTION) {
        return route($route, [$slug, Status::STEP_GENERAL]);
    } elseif ($currentStep == Status::STEP_SEO) {
        return route($route, [$slug, Status::STEP_DESCRIPTION]);
    } elseif ($currentStep == Status::STEP_VARIANTS) {
        return route($route, [$slug, Status::STEP_SEO]);
    } elseif ($currentStep == Status::STEP_MEDIA) {
        $previousStep = $productType == Status::PRODUCT_TYPE_VARIABLE
            ? Status::STEP_VARIANTS
            : Status::STEP_SEO;
        return route($route, [$slug, $previousStep]);
    }
}

function getSessionId() {
    $sessionId = session('session_id');
    if (!$sessionId) {
        $sessionId = uniqid();
        session()->put('session_id', $sessionId);
    }
    return $sessionId;
}

function getOrderNumber($digit = 8) {
    $prefix = 'OID-';
    $last = Order::max('id') + 1;
    $formattedLast = str_pad($last, $digit, '0', STR_PAD_LEFT);
    return $prefix . $formattedLast;
}

function updateCartUserId() {
    Cart::where('session_id', getSessionId())->update(['user_id' => auth()->id()]);
}
function getCartData() {
    $queryProduct = Cart::with('product', 'productVariant', 'product.vendor', 'cartPrintAreas');
    if (auth()->check()) {
        $queryProduct->where('user_id', auth()->id());
    } else {
        $queryProduct->where('session_id', getSessionId());
    }
    return $queryProduct->get();
}

function deleteCartData() {
    $queryProduct = Cart::query();
    if (auth()->check()) {
        $queryProduct->where('user_id', auth()->id());
    } else {
        $queryProduct->where('session_id', getSessionId());
    }
    $queryProduct->delete();
}

function pixelsToInches($pixel) {
    return $pixel / gs('print_area_dpi');
}

function averageRating($totalStars, $totalReview) {
    return $totalReview > 0
        ? rtrim(rtrim(number_format(($totalStars / $totalReview), 1, '.', ''), '0'), '.')
        : 0;
}

function showRating($rating) {
    $html = '<ul class="rating-list rating-three">';
    for ($i = 1; $i <= 5; $i++) {
        $disabled = $i <= $rating ? "fa-solid" : "fa-regular";
        $html .= '<li class="rating-list__item">
                <span class="icon"><i class="' . $disabled . ' fa-star"></i></span>
            </li>';
    }

    $html .= '</ul>';
    return $html;
}
