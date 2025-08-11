<?php
/**
 * The file was created by Assimon.
 *
 */


use App\Exceptions\AppException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;

if (! function_exists('replaceMailTemplate')) {

    /**
     * 替换邮件模板
     *
     * @param array $template 模板
     * @param array $data 内容
     * @return array|false|mixed
     *
     */
    function replaceMailTemplate($template = [], $data = [])
    {
        if (!$template) {
            return false;
        }
        if ($data) {
            foreach ($data as $key => $val) {
                $title = str_replace('{' . $key . '}', $val, isset($title) ? $title : $template['tpl_name']);
                $content = str_replace('{' . $key . '}', $val, isset($content) ? $content : $template['tpl_content']);
            }
            return ['tpl_name' => $title, 'tpl_content' => $content];
        }
        return $template;
    }
}


if (! function_exists('cfg')) {
    function cfg(string $key, $default = null)
    {
       return app('App\\Services\\ConfigService')->get($key, $default);
    }
}

if (! function_exists('theme_config')) {
    function theme_config(string $key, $default = null)
    {
       return app('App\Services\ThemeService')->getConfig($key, $default);
    }
}

if (! function_exists('theme_asset')) {
    function theme_asset(string $path): string
    {
       return app('App\Services\ThemeService')->asset($path);
    }
}

if (! function_exists('current_theme')) {
    function current_theme(): string
    {
       return app('App\Services\ThemeService')->getCurrentTheme();
    }
}

if (! function_exists('shop_cfg')) {
    function shop_cfg(string $key, $default = null)
    {
        return app('App\Settings\ShopSettings')->{$key} ?? $default;
    }
}

if (! function_exists('theme_cfg')) {
    function theme_cfg(string $key, $default = null)
    {
        return app('App\Settings\ThemeSettings')->{$key} ?? $default;
    }
}


if (! function_exists('formatWholesalePrice')) {

    /**
     * 格式化批发价
     *
     * @param string $priceConfig 批发价配置
     * @return array|null
     *
     */
    function formatWholesalePrice(string $priceConfig): ?array
    {
        $waitArr = explode(PHP_EOL, $priceConfig);
        $formatData = [];
        foreach ($waitArr as $key => $val) {
            if ($val != "") {
                $explodeFormat = explode('=', cleanHtml($val));
                if (count($explodeFormat) != 2) {
                    return null;
                }
                $formatData[$key]['number'] = $explodeFormat[0];
                $formatData[$key]['price'] = $explodeFormat[1];
            }
        }
        sort($formatData);
        return $formatData;
    }
}

if (! function_exists('cleanHtml')) {

    /**
     * 去除html内容
     * @param string $str 需要去掉的字符串
     * @return string
     */
    function cleanHtml(string $str): string
    {
        $str = trim($str); //清除字符串两边的空格
        $str = preg_replace("/\t/", "", $str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
        $str = preg_replace("/\r\n/", "", $str);
        $str = preg_replace("/\r/", "", $str);
        $str = preg_replace("/\n/", "", $str);
        $str = preg_replace("/ /", "", $str);
        $str = preg_replace("/  /", "", $str);  //匹配html中的空格
        return trim($str); //返回字符串
    }
}

if (! function_exists('formatChargeInput')) {

    /**
     * 格式化代充框
     *
     * @param string $charge
     * @return array|null
     *
     */
    function formatChargeInput(string $charge): ?array
    {
        $inputArr = explode(PHP_EOL, $charge);
        $formatData = [];
        foreach ($inputArr as $key => $val) {
            if ($val != "") {
                $explodeFormat = explode('=', cleanHtml($val));
                if (count($explodeFormat) != 3) {
                    return null;
                }
                $formatData[$key]['field'] = $explodeFormat[0];
                $formatData[$key]['desc'] = $explodeFormat[1];
                $formatData[$key]['rule'] = filter_var($explodeFormat[2], FILTER_VALIDATE_BOOLEAN);
            }
        }
        return $formatData;
    }
}

if (! function_exists('siteUrl')) {

    /**
     * 获取顶级域名 带协议
     * @return string
     */
    function siteUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'] . '/';
        return $protocol . $domainName;
    }
}

if (! function_exists('md5SignQuery')) {

    function md5SignQuery(array $parameter, string $signKey)
    {
        ksort($parameter); //重新排序$data数组
        reset($parameter); //内部指针指向数组中的第一个元素
        $sign = '';
        $urls = '';
        foreach ($parameter as $key => $val) {
            if ($val == '') continue;
            if ($key != 'sign') {
                if ($sign != '') {
                    $sign .= "&";
                    $urls .= "&";
                }
                $sign .= "$key=$val"; //拼接为url参数形式
                $urls .= "$key=" . urlencode($val); //拼接为url参数形式
            }
        }
        $sign = md5($sign . $signKey);//密码追加进入开始MD5签名
        $query = $urls . '&sign=' . $sign; //创建订单所需的参数
        return $query;
    }
}

if (! function_exists('signQueryString')) {

    function signQueryString(array $data)
    {
        ksort($data); //排序post参数
        reset($data); //内部指针指向数组中的第一个元素
        $sign = ''; //加密字符串初始化
        foreach ($data as $key => $val) {
            if ($val == '' || $key == 'sign') continue; //跳过这些不签名
            if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
            $sign .= "$key=$val"; //拼接为url参数形式
        }
        return $sign;
    }
}

if (!function_exists('pictureUrl')) {

    /**
     * 生成前台图片链接 不存在使用默认图
     * 如果地址已经是完整URL了，则直接输出
     * @param string $file 图片地址
     * @param false $getHost 是否只获取图片前缀域名
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function pictureUrl($file, $getHost = false)
    {
        if ($getHost) return Storage::disk('admin')->url('');
        if (Illuminate\Support\Facades\URL::isValidUrl($file)) return $file;
        return $file ? Storage::disk('admin')->url($file) : url('assets/common/images/default.jpg');
    }
}

if (!function_exists('assocUnique')) {
    function assocUnique($arr, $key)
    {
        $tmp_arr = array();
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                unset($arr[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        sort($arr); //sort函数对数组进行排序
        return $arr;
    }
}

if (!function_exists('getIpCountry')) {
    function getIpCountry($ip) {
        // 对Cloudflare站点的支持优化
        if(isset($_SERVER["HTTP_CF_IPCOUNTRY"]))
            $isoCode = $_SERVER["HTTP_CF_IPCOUNTRY"];
        else{
            $reader = new Reader(storage_path('app/library/GeoLite2-Country.mmdb'));
            try {
                $isoCode = $reader->country($ip)->country->isoCode;
            }catch(AddressNotFoundException $e){
                $isoCode = "";
            }
        }
        
        return $isoCode;
    }
}