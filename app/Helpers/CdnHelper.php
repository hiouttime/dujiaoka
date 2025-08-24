<?php

namespace App\Helpers;

use App\Settings\SystemSettings;

class CdnHelper
{
    /**
     * 替换静态资源URL为CDN地址
     *
     * @param string $url 原始URL
     * @return string 处理后的URL
     */
    public static function asset(string $url): string
    {
        $cdnUrl = app(SystemSettings::class)->cdn_url;
        
        // 如果没有配置CDN URL，直接返回原URL
        if (empty($cdnUrl)) {
            return $url;
        }
        
        // 移除CDN URL末尾的斜杠
        $cdnUrl = rtrim($cdnUrl, '/');
        
        // 如果是绝对URL且是本站资源，替换域名部分
        if (str_starts_with($url, request()->getSchemeAndHttpHost())) {
            return str_replace(request()->getSchemeAndHttpHost(), $cdnUrl, $url);
        }
        
        // 如果是相对URL，添加CDN域名
        if (str_starts_with($url, '/')) {
            return $cdnUrl . $url;
        }
        
        // 其他情况直接返回原URL
        return $url;
    }
}