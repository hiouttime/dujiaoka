<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\BatchRestore;
use App\Admin\Actions\Post\Restore;
use App\Admin\Repositories\RemoteServer;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Admin;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\RemoteServer as RemoteServerModel;

class RemoteServerController extends AdminController
{

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new RemoteServer(), function (Grid $grid) {
            $grid->model();
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('type')
                ->using(RemoteServerModel::getServerTypeMap())
                ->label([
                    RemoteServerModel::HTTP_SERVER =>Admin::color()->info(),
                    RemoteServerModel::RCON_COMMAND =>Admin::color()->info(),
                    RemoteServerModel::SQL_EXECUTE =>Admin::color()->info(),
                ]);
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->scope(admin_trans('dujiaoka.trashed'))->onlyTrashed();
            });
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if (request('_scope_') == admin_trans('dujiaoka.trashed')) {
                    $actions->append(new Restore(RemoteServerModel::class));
                }
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new RemoteServer(), function (Show $show) {
            $show->field('id');
            $show->field('gp_name');
            $show->field('is_open')->as(function ($isOpen) {
                if ($isOpen == RemoteServerModel::STATUS_OPEN) {
                    return admin_trans('dujiaoka.status_open');
                } else {
                    return admin_trans('dujiaoka.status_close');
                }
            });
            $show->field('ord');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new RemoteServer(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->radio('type')->options(RemoteServerModel::getServerTypeMap())->default(RemoteServerModel::HTTP_SERVER)->required()
            ->help(admin_trans('remote-server.helps.type'))
            ->when(RemoteServerModel::HTTP_SERVER, function (Form $form) {
                $form->url('http_url');
                $form->text('http_method')->default("GET")->help(admin_trans('remote-server.helps.http_method'));
                $form->textarea('request')->help(admin_trans('remote-server.helps.http_request'));
                $form->text('http_return')->help(admin_trans('remote-server.helps.http_return'));
            })
            ->when(RemoteServerModel::RCON_COMMAND, function (Form $form) {
                $form->text('rcon_host');
                $form->text('rcon_port');
                $form->password('rcon_pass');
                $form->textarea('command')->help(admin_trans('remote-server.helps.rcon_command'));
                $form->text('rcon_return')->help(admin_trans('remote-server.helps.rcon_return'));
            })
            ->when(RemoteServerModel::SQL_EXECUTE, function (Form $form) {
                $form->text('db_host');
                $form->text('db_user');
                $form->password('db_pass');
                $form->text('db_name');
                $form->textarea('db_sql');
            });
            $form->html('<div class="alert alert-info">
            '.admin_trans('remote-server.you_can_use').'
            <li><code>{$title}</code> '.admin_trans('order.fields.title').'</li>
            <li><code>{$gd_name}</code> '.admin_trans('goods.fields.gd_name').'</li>
            <li><code>{$order_sn}</code> '.admin_trans('order.fields.order_sn').'</li>
            <li><code>{$email}</code> '.admin_trans('order.fields.email').'</li>
            <li><code>{$price}</code> '.admin_trans('order.fields.total_price').'</li>
            <li><code>{$唯一标识}</code> 商品其他输入框配置的变量</li>
            </div>', $label = '');
            $form->display('created_at');
            $form->display('updated_at');
            $form->hidden('data');
            $form->saving(function (Form $form) {
                $data = $form->input();
                
                unset($data['name'], $data['type'], $data['_token']);
                $form->data = serialize($data);
                foreach (array_keys($data) as $k => $v)
                    $form->deleteInput($v);
            });
            if ($form->isEditing()) {
                $id = $form->getKey(); // 获取当前编辑的记录ID
                $id = RemoteServerModel::find($id); // 根据ID查询记录
                    if ($id) {
                        $data = unserialize($id->data); // 反序列化数据
                        foreach ($data as $key => $value) 
                            $form->model()->$key = $value;
                }
            }
        });
    }
}
