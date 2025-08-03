<?php
/**
 * The file was created by Assimon.
 *
 */

namespace App\Http\Controllers;

class BaseController extends Controller
{

    /**
     * 渲染模板
     *
     * @param string $tpl 模板名称
     * @param array $data 数据
     * @param array $pageTitle 页面标题
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    protected function render(string $tpl, $data = [], string $pageTitle = '')
    {
        $theme = current_theme();
        $data["currency"] = cfg('currency', 'cny') === 'usd' ? "&#xe704;" : "&#xe703;";
        
        return view("{$theme}::{$tpl}", $data)->with('page_title', $pageTitle);
    }

    /**
     * 错误提示
     *
     * @param string $content 提示内容
     * @param string $jumpUri 跳转url
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    protected function err(string $content, $jumpUri = '')
    {
        $theme = current_theme();
        return view("{$theme}::errors/error", [
            'title' => __('dujiaoka.error_title'), 
            'content' => $content, 
            'url' => $jumpUri
        ])->with('page_title', __('dujiaoka.error_title'));
    }

}
