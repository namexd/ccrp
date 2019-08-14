<?php

namespace App\Utils;

use App\Models\Ccrp\Company;
use Carbon\Carbon;

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
function creat_deliverorder($company_id)

{

    //10000011511212151123

    return sprintf("%04d", $company_id) . date('ymdHis');

}
function format_value($value, $none = null, $decimal = 1, $suffix = '')
{
    if ($value !== '' and $value !== NULL and $value <> -999){
        return sprintf("%.".$decimal."f", round($value, 3)) . $suffix;
    }
    else{
        return $none;
    }
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
/**
 * 加密
 * @param $data
 * @param $key
 * @return string
 */
function encrypt($data, $key)
{
    $char = $str = '';
    $key = md5($key);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= $key{$x};
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
    }
    return base64_encode($str);
}


/**
 * app加密认证
 * @param $appkey
 * @param $appsecret
 * @param $info
 * @return string
 */
function app_access_encode($appkey, $appsecret, $info)
{
    return encrypt(time() . '|||' . strtoupper($appkey) . '|||' . strtoupper($appsecret) . '|||' . json_encode($info), $appkey);
}


function domain_fix()

{

    //自定义网址

    $domain = $_SERVER['HTTP_HOST'];

    $cfg_domain = Company::ONLINE_DOMAIN;
    $cfg_domain2 =Company::ONLINE_DOMAIN2;

    if (in_array($domain, $cfg_domain)) {

        return array_search($domain, $cfg_domain);

    } elseif (in_array($domain, $cfg_domain2)) {

        return array_search($domain, $cfg_domain2);

    } else return '';

}

/**
 * 解密
 * @param $data
 * @param $key
 * @return string
 */
function decrypt($data, $key)
{

    $char = $str = '';
    $key = md5($key);
    $x = 0;
    $replaces = array(' ' => '+');
    $data = base64_decode(strtr($data, $replaces));
    $len = strlen($data);
    $l = strlen($key);
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return $str;
}

/**
 * app解密认证
 * @param $appkey
 * @param $appsecret
 * @param $access
 * @param int $checktime
 * @return string
 */
function app_access_decode($appkey, $appsecret, $access, $checktime = 0)
{
    $data = decrypt($access, $appkey);
    $data = explode("|||", $data);
    if ($checktime and isset($data[0]) and (abs(time() - $data[0]) > $checktime)) {
        return null;
    }
    if (isset($data[2]) and $data[2] == strtoupper($appsecret)) {
        return json_decode($data[3],true);
    }
    return null;
}

/**
 * 隐藏手机号中间4位
 * @param $str
 * @return string
 */
function hidePhone($str)
{
    if(strpos($str,','))
    {
        $phones = explode(',',$str);
        $res = '';
        foreach($phones as $phone)
        {
            $res .= hidePhone($phone).',';
        }
    }else{
        $res = strlen($str)>0?substr($str, 0, 3) . '****' . substr($str, -4):'';
    }
    return $res;
}
function vehicle_time($time)

{
    return str_replace(array('T', '+08:00'), array(' ', ' '), $time);

}

function vehicle_time2($time)

{

    return date('m-d H:i', strtotime(str_replace(array('T', '+08:00'), array(' ', ' '), $time)));

}
function vehicle_temp2($temp)

{

    $temp = floatval($temp);

    if ($temp <> -999) {

        return sprintf("%.1f", round($temp, 3));

    } else return '-';

}
//定制
function is_com_diy($company_id, $com = 'minhang')
{
    $slug = $com;
    $function = CompanyFunction::where(array('slug' => $slug))->first();
    if ($function) {
        $has_function = CompanyHasFunction::where(array('company_id' => $company_id, 'function_id' => $function['id']))->first();

        if ($has_function) return true;
    }

    $arrs = array(

        //便携式不打印时间 OK
        'printer_no_time' => array(597, 369, 632, 1269), //重庆医股,山西执信OK

        //闵行区 2018年02月27日 OK
        'minhang' => array(601, 605, 606, 607, 608, 609, 611, 612, 613, 614, 615, 616, 617, 618, 619, 620, 621, 622, 623, 624, 625, 626, 627, 628, 710, 1595, 2416, 601, 1043, 1044, 1045, 1046, 1047),
        //OK
        'daochu30' => array(
            //宝山区 2018年06月04日 == 试用
            386, 388, 389, 390, 391, 392, 393, 394, 395, 397, 400, 401, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 416, 417, 418, 419, 421, 442, 443, 444, 445, 446, 447, 2548,
            //乐山市市中区 2018年06月07日 == 试用
            1128, 1091,
        ),
        //OK
        //人工测温记录 2018年02月27日
        //1，测试，花都，松江，蒙城，闵行，徐汇
        'rgcwjl' => array(
            1,
            //花都区
            1616,
            1617, 1618, 1619, 1620, 1621, 1622, 1623, 1624, 1625, 1626, 1627, 1628, 1629, 1630, 1631, 1632, 1633, 1634, 1635, 1636, 1637, 1638, 1639, 1640, 1641, 1642, 1643, 1644, 1645, 1646, 1647, 1648, 1655, 1656, 1657, 1658, 1659, 1660, 1661, 1662, 1663, 1664, 1665, 1666, 1667, 1668, 1669, 1670, 1671, 1672, 1673, 1674, 1675, 1676, 1677, 1678,
            //松江区
            827,
            828, 829, 830, 831, 832, 833, 834, 835, 836, 838, 839, 840, 841, 842, 843, 844, 845, 846, 847, 848, 849, 850, 851, 852, 853, 854, 855, 905, 1041, 1042,
            //蒙城县
            239, 240, 241, 242, 243, 244, 245, 246, 247, 248, 249, 250, 251, 252, 253, 254, 255, 256, 257, 258, 259, 260, 261, 262, 263, 264, 1520, 1521, 1522, 1523, 1524, 1525, 1526, 1527, 1528, 1612, 1613, 235, 1608, 1611, 139,
            //闵行区 2018年04月20日
            602, 605, 606, 607, 608, 609, 610, 611, 612, 613, 614, 615, 616, 617, 618, 619, 620, 621, 622, 623, 624, 625, 626, 627, 628, 710, 722, 1595, 2416, 601,
            // 徐汇区 2018年04月20日
            915, 916, 918, 919, 920, 921, 922, 923, 924, 925, 926, 927, 928, 929, 930, 931, 932, 933, 1048, 1051, 1052, 1080, 2281, 2282, 2418, 2419, 2425, 908, 2822,
            // 杨浦区 2018年06月28日
            744, 745, 746, 747, 748, 749, 750, 751, 752, 753, 754, 755, 756, 757, 758, 759, 760, 761, 1165, 1374, 2417, 2442, 723,
            // 长宁区 2019年2月18日
            1325, 1326, 1327, 1328, 1329, 1330, 1331, 1332, 1333, 1334, 1335, 1348, 2433, 3478, 1324,
            //四川省疾控中心
            183,
            //宝山区  2018年06月04日 == 试用
            388, 389, 390, 391, 392, 393, 394, 395, 397, 400, 401, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 416, 417, 418, 419, 421, 442, 443, 444, 445, 446, 447, 2548,
            //乐山市市中区 2018年06月07日 == 试用
            1128, 1091,
            //市中区   2018年06月28日
            1091, 1129, 1972, 1973, 1974, 1975, 1976, 1977, 1978, 1979, 1980, 1981, 1982, 1983, 1984, 1985, 1986, 1987, 1988, 1989, 1990, 1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999, 2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024, 2183, 1128,
            //苏州翊曼
            2439,
            //软件测试
            2441, 3443,
            //富阳区疾病预防控制中心免疫规划科,2019年2月25日
            1456,
        ),
        //花都区OK
        'gzhuadu' => array(1617, 1618, 1619, 1620, 1621, 1622, 1623, 1624, 1625, 1626, 1627, 1628, 1629, 1630, 1631, 1632, 1633, 1634, 1635, 1636, 1637, 1638, 1639, 1640, 1641, 1642, 1643, 1644, 1645, 1646, 1647, 1648, 1655, 1656, 1657, 1658, 1659, 1660, 1661, 1662, 1663, 1664, 1665, 1666, 1667, 1668, 1669, 1670, 1671, 1672, 1673, 1674, 1675, 1676, 1677, 1678),
        //天津市OK
        'tjcdc' => array(675, 689, 690, 691, 692, 693, 1270, 1708, 1709, 1710, 1711, 1712, 1713, 1714, 1715, 1716, 1717, 1718, 1719, 1720, 1721, 1722, 1723, 1724, 1725, 1726, 1727, 1728, 1729, 1730, 1731, 1732, 1733, 1734, 1735, 1736, 1737, 1738, 1739, 1740, 1741, 1742, 1743, 1744, 1745, 1746, 1747, 1748, 1749, 1750, 1751, 1752, 1753, 1754, 1755, 1756, 1757, 1758, 1759, 1760, 1761, 1762, 1763, 1764, 1765, 1766, 1767, 1768, 1769, 1771, 1772, 1773, 1774, 1775, 1776, 1777, 1778, 1779, 1780, 1781, 1782, 1783, 1784, 1785, 1786, 1787, 1788, 1789, 1790, 1791, 1792, 1793, 1794, 1795, 1796, 1797, 1798, 1799, 1800, 1801, 1802, 1803, 1804, 1805, 1806, 1807, 1808, 1809, 1810, 1811, 1812, 1813, 1814, 1815, 1816, 1817, 1818, 1819, 1820, 1821, 1822, 1823, 1824, 1825, 1826, 1827, 1828, 1829, 1830, 1831, 1832, 1833, 1834, 1835, 1836, 1837, 1838, 1839, 1840, 1841, 1842, 1843, 1844, 1845, 1846, 1847, 1848, 1849, 1850, 1851, 1852, 1853, 1854, 1855, 1856, 1857, 1858, 1859, 1860, 1861, 1862, 1863, 1864, 1865, 1866, 1867, 1868, 1869, 1870, 1871, 1872, 1873, 1874, 1875, 1876, 1877, 1878, 1879, 1880, 1881, 1882, 1883, 1884, 1885, 1886, 1887, 1888, 1889, 1890, 1891, 1892, 1893, 1894, 1895, 1896, 1897, 1898, 1899, 1900, 1901, 1902, 1903, 1904, 1905, 1906, 1907, 1908, 1909, 1910, 1911, 1912, 1913, 1914, 1915, 1916, 1917, 1918, 1919, 1920, 1921, 1922, 1923, 1924, 1925, 1926, 1927, 1928, 1929, 1930, 1931, 1932, 1933, 1934, 1935, 1936, 1937, 1938, 1939, 1940, 1941, 1942, 1943, 1944, 1945, 1946, 1947, 1948, 1949, 1950, 1951, 1952, 1953, 1954, 1955, 1956, 1957, 1958, 1959, 1960, 1961, 1962, 1963, 1964, 1965, 1966, 1967, 1968, 1969, 1970, 1971, 2032, 2033, 2034, 2035, 2036, 2037, 2038, 2039, 2040, 2041, 2042, 2043, 2044, 2045, 2046, 2047, 2048, 2049, 2050, 2051, 2052, 2053, 2054, 2055, 2056, 2057, 2058, 2059, 2060, 2061, 2062, 2063, 2064, 2065, 2066, 2067, 2068, 2069, 2070, 2071, 2072, 2073, 2074, 2075, 2076, 2077, 2078, 2079, 2080, 2081, 2082, 2083, 2084, 2085, 2086, 2087, 2088, 2089, 2090, 2091, 2092, 2093, 2094, 2095, 2096, 2097, 2098, 2099, 2100, 2101, 2102, 2103, 2104, 2105, 2106, 2107, 2108, 2109, 2110, 2111, 2112, 2113, 2114, 2115, 2116, 2117, 2118, 2119, 2120, 2121, 2122, 2123, 2124, 2125, 2126, 2127, 2128, 2129, 2130, 2131, 2132, 2133, 2134, 2135, 2136, 2137, 2138, 2139, 2140, 2141, 2142, 2143, 2144, 2145, 2146, 2147, 2148, 2149, 2150, 2151, 2152, 2153, 2154, 2155, 2156, 2157, 2158, 2159, 2160, 2161, 2162, 2163, 2164, 2165, 2166, 2167, 2168, 2169, 2170, 2171, 2181, 2422, 2521, 2522, 2539, 2540, 2541, 2568, 2570, 2573, 2650, 2677, 2676, 2675, 2748, 2802, 2801, 2803, 2807, 2811, 2814, 2824, 2825, 2827, 2828, 2829, 2834, 2839, 2838, 2842, 2844, 2841, 2866, 2868, 2659, 2870, 2930, 2933, 2953, 3432, 3433, 3436, 2547, 3442, 3445, 2577, 3452, 3451, 3666, 3670, 3678, 3692, 3745, 3763, 3762,
            658, 659, 660, 661, 662, 663, 664, 665, 666, 667, 668, 669, 670, 671, 672, 673, 674, 675, 1704, 1705, 1706, 1707, 1770,
            //管理单位
            //软件测试
            3443,

        ),
        //OK
        'dgcdc' => array(17, 18, 19, 20, 21, 22, 23, 24, 25, 485, 486, 487, 488, 489, 490, 492, 494, 495, 496, 497, 498, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514, 515, 516, 517, 518, 519, 520, 521, 522, 523, 524, 525, 526, 527, 528, 529, 530, 531, 532, 533, 534, 535, 536, 537, 538, 539, 540, 541, 542, 543, 544, 545, 546, 547, 548, 549, 550, 551, 552, 553, 554, 555, 556, 557, 558, 559, 560, 561, 562, 563, 564, 565, 566, 567, 568, 569, 570, 571, 572, 573, 574, 575, 576, 577, 578, 579, 580, 581, 582, 583, 584, 585, 586, 587, 588, 590, 593, 1029, 1117, 1162, 1256, 1425, 1550, 2202, 2204, 2267,
            26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 484, 589, 1087,//管理
        ),
        //异常事件处置记录 NONONO
        'ycwdczjl' =>
            array(
                726,//726是硬件测试的
                2441,//rjcs
            ),

        //康德乐OK
        'kdl' => array(141),
        //微信保温箱开启延迟订单 OK
        'wx_create_time_last' => array(2448, 1, 2468, 2588),
        //重庆医股,打印后电子签名,没有使用 NONONO
        'print_sign' => array(1),
        //重庆医股,打印带,没有使用
        'print_summary' => array(1, 597),
        'cq_yy' => array(597),

    );
    if (isset($arrs[$com]) and in_array($company_id, $arrs[$com])) {
        return true;
    }

    return false;
}
function to_shidu($value = '', $fix = '%')

{

    if ($value !== '' and $value !== NULL and $value <> -999 and $value <> 0) {
        if ($value > 100) $value = 100;
        return sprintf("%.1f", round($value, 3)) . $fix;
    } else return '-';

}

function to_wendu($value, $fix = '℃')

{

    if ($value !== '' and $value !== NULL and $value > -999) return sprintf("%.1f", round($value, 3)) . $fix;

    else return '-';

}

function to_wendu2($value, $fix = '℃')

{

    if ($value !== '' and $value !== NULL and $value > -999) return sprintf("%.2f", round($value, 3)) . $fix;

    else return '-';

}

function to_danya($value, $fix = 'V')

{

    if ($value !== '' and $value !== NULL and $value <> -999) return $value . $fix;

    else return '-';

}

function to_dianya($value, $fix = 'V')

{

    if ($value !== '' and $value !== NULL and $value <> -999) return $value . $fix;

    else return '-';

}

function to_rssi($value)

{

    if ($value !== '' and $value !== NULL and $value <> -999) return $value . '';

    else return '-';

}

function to_dianliang($value, $fix = '%')
{
    if ($value !== '' and $value !== NULL and $value <> -999) return $value . $fix;

    else return '-';
}
function time_format2($time = NULL, $format = 'Y-m-d H:i:s', $zero = '-')
{


    if ($time === NULL or $time <= 0) return $zero;

    else return date($format, intval($time));

}
//去除符号-
function abs2($str)
{
    if (gettype($str) == 'integer') return abs($str);
    else return trim(strval(str_replace('-', '', $str)));
}

function sortArrByField(&$array, $field, $desc = false)
{

    $fieldArr = array();

    foreach ($array as $k => $v) {

        $fieldArr[$k] = $v[$field];

    }

    $sort = $desc == false ? SORT_DESC : SORT_ASC;

    array_multisort($fieldArr, $sort, $array);

}

function loginkey()
{
    return 'D17F6' . strtoupper(substr(md5(date('dmy')), 0, 8));
}

function format_time($value)
{
    if ($value) {
        return Carbon::createFromTimestamp($value)->toDateTimeString();
    } else {
        return '-';
    }
}
