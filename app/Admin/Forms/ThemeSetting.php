<?php

namespace App\Admin\Forms;

use App\Models\BaseModel;
use Dcat\Admin\Widgets\Form;
use Illuminate\Support\Facades\Cache;

class ThemeSetting extends Form
{
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        Cache::put('theme-setting', $input);
        return $this
				->response()
				->success(admin_trans('admin.save_succeeded'));
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->tab(admin_trans('system-setting.labels.base_setting'), function () {
            $this->textarea('notice', admin_trans('theme-setting.fields.notice'))->help(admin_trans('theme-setting.helps.notice'));
        });
    }

    public function default()
    {
        return Cache::get('theme-setting');
    }

}
