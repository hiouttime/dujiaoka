<?php
/**
 * The file was created by Outtime.
 *
 * @author    outtime<beprivacy@icloud.com>
 * @copyright outtime<beprivacy@icloud.com>
 * @link      https://outti.me
 */

namespace App\Admin\Controllers;


use App\Admin\Forms\ThemeSetting;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Card;

class ThemeSettingController extends AdminController
{

    /**
     * 主题设置
     *
     * @param Content $content
     * @return Content
     *
     * @author    outtime<beprivacy@icloud.com>
     * @copyright outtime<beprivacy@icloud.com>
     * @link      https://outti.me
     */
    public function themeSetting(Content $content)
    {
        return $content
            ->title(admin_trans('menu.titles.theme_setting'))
            ->body(new Card(new ThemeSetting()));
    }

}
