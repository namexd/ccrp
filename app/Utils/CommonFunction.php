<?php

namespace App\Utils;

function array_trim($arr, $trim = true)
{
    if (!is_array($arr)) return $arr;
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            array_trim($arr[$key]);
        } else {
            $value = ($trim == true) ? trim($value) : $value;
            if ($value == "") {
                unset($arr[$key]);
            } else {
                $arr[$key] = $value;
            }
        }
    }
    return $arr;
}

function format_value($value, $suffix = '')
{
    if ($value !== '' and $value !== NULL and $value <> -999) return sprintf("%.1f", round($value, 3)) . $suffix;
    else return '-';
}

/*
 * 阿里云短信-发送验证码
 */
function send_vcode($phone, $code, $product = '冷链资源管理系统')
{
    $smsService = \App::make(\Curder\LaravelAliyunSms\AliyunSms::class);
    $tplId = config('api.defaults.sms.aliyun.template.vcode');
    $params = [
        'code' => $code,
        'product' => $product,
    ];
    $rs = $smsService->send(strval($phone), $tplId, $params);
    return $rs;
}


/**
 * 最近X个月的月份(不含当前月）：201608，201609...
 * @param int $length 几个月
 * @param null $year_month 初始月 201608
 * @param string $format
 * @return array
 */
function get_last_months($length = 6, $year_month = null, $format = "Ym")
{
    if ($year_month == null) $year_month = date("Ym");
    $year = substr($year_month, 0, 4);
    $month = substr($year_month, -2);
    $data = $year . '-' . $month . '-01 00:00:00';
    $arr = [];
    for ($i = $length; $i > 0; $i--) {
        $arr[] = date($format, strtotime($data . ' -' . $i . ' month'));
    }
    return $arr;
}

function get_month_first($date = null)
{
    if ($date == null) {
        $date = date('Y-m-01');
    } elseif ($date == 'this') {
        return date('Y-m-01');
    }
    $timestamp = strtotime($date);
    $firstday = date('Y-m-01', strtotime(date('Y', $timestamp) . '-' . (date('m', $timestamp) - 1) . '-01'));
    return $firstday;
}

function get_month_last($date = null)
{
    if ($date == null) {
        $date = date('Y-m-01');
    } elseif ($date == 'this') {
        $firstday = date('Y-m-01');
        return date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    }
    $timestamp = strtotime($date);
    $firstday = date('Y-m-01', strtotime(date('Y', $timestamp) . '-' . (date('m', $timestamp) - 1) . '-01'));
    $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    return $lastday;
}

/**
 * 往url里添加参数
 * @param $url
 * @param $key
 * @param $value
 * @return string
 */
function add_query_param($url, $key, $value)
{
    $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
    $url = substr($url, 0, -1);
    if (strpos($url, '?') === false) {
        return ($url . '?' . $key . '=' . $value);
    } else {
        return ($url . '&' . $key . '=' . $value);
    }
}


//判断是否是移动端访问
function is_mobile()
{
// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return TRUE;
    }
// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], "wap") ? TRUE : FALSE;// 找不到为flase,否则为TRUE
    }
// 判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'mobile',
            'nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return TRUE;
        }
    }
    if (isset ($_SERVER['HTTP_ACCEPT'])) { // 协议法，因为有可能不准确，放到最后判断
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === FALSE || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return TRUE;
        }
    }
    return FALSE;
}


function time_clock($clock = 0, $date = NULL)
{
    if ($date == NULL) {
        $date = date('Y-m-d');
    }
    if ($clock == 0) {
        $rs = strtotime($date . ' 00:00:00');
    } elseif ($clock == 24) {
        $rs = strtotime($date . ' 23:59:59') + 1;
    } else {
        $rs = strtotime($date . ' ' . $clock . ':00:00');
    }
    return $rs;

}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 */
function time_format($time = NULL, $format = 'Y-m-d H:i')
{
    $time = $time === NULL ? time() : intval($time);
    return date($format, $time);
}

/**
 * 获取指定日期段内每一天的日期
 * @param  Date $startdate 开始日期
 * @param  Date $enddate 结束日期
 * @return Array
 */
function getDays($startdate, $enddate, $format = 'Y-m-d')
{

    $stimestamp = strtotime($startdate);
    $etimestamp = strtotime($enddate);

    // 计算日期段内有多少天
    $days = ($etimestamp - $stimestamp) / 86400;

    // 保存每天日期
    $date = array();

    for ($i = 0; $i < $days; $i++) {
        $date[] = date($format, $stimestamp + (86400 * $i));
    }

    return $date;
}

/**
 * 微服务内网加密认证
 * @param $appkey
 * @param $appsecret
 * @param $info
 * @return string
 */
function microservice_access_encode($appkey, $appsecret, $info)
{
    return base64_encode(time() . '|||' . strtoupper($appkey) . '|||' . strtoupper($appsecret) . '|||' . json_encode($info));
}

/**
 * 微服务内网解密认证
 * @param $access
 * @return string
 */
function microservice_access_decode($access)
{
    $data = base64_decode($access);
    if (!$data) {
        return null;
    }
    $data = explode("|||", $data);
    $response['time'] = $data[0] ?? 0;
    $response['appkey'] = $data[1] ?? null;
    $response['appsecret'] = $data[2] ?? null;
    $response['info'] = isset($data[3])?json_decode($data[3], true) : null;
    return $response;
}
