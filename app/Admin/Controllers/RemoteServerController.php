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
            $form->fields = [
                RemoteServerModel::HTTP_SERVER => ['http_url'=>'url', 'http_method'=>'text', 'request'=>'textarea', 'http_return'=>'textarea'],
                RemoteServerModel::RCON_COMMAND => ['rcon_host'=>'text', 'rcon_port'=>'number', 'rcon_pass'=>'password', 'command'=>'textarea', 'rcon_return'=>'textarea'],
                RemoteServerModel::SQL_EXECUTE => ['db_host'=>'text', 'db_user'=>'text', 'db_pass'=>'password', 'db_name'=>'text', 'db_sql'=>'textarea'],
            ];
            $form->radio('type')->options(RemoteServerModel::getServerTypeMap())->default(RemoteServerModel::HTTP_SERVER)->required()
            ->help(admin_trans('remote-server.helps.type'))
            ->when(RemoteServerModel::HTTP_SERVER, function (Form $form) {
                foreach ($form->fields[RemoteServerModel::HTTP_SERVER] as $k => $v) {
                    $form->$v($k)
                    ->rules('required_if:type,'.RemoteServerModel::HTTP_SERVER)
                    ->customFormat(function () use ($k) {
                        return unserialize($this->data)[$k] ?? null;
                    });
                }
            })
            ->when(RemoteServerModel::RCON_COMMAND, function (Form $form) {
                foreach ($form->fields[RemoteServerModel::RCON_COMMAND] as $k => $v) {
                    $form->$v($k)
                    ->rules('required_if:type,'.RemoteServerModel::RCON_COMMAND)
                    ->customFormat(function () use ($k) {
                        return unserialize($this->data)[$k] ?? null;
                    });
                }
            })
            ->when(RemoteServerModel::SQL_EXECUTE, function (Form $form) {
                foreach ($form->fields[RemoteServerModel::SQL_EXECUTE] as $k => $v) {
                    $form->$v($k)
                    ->rules('required_if:type,'.RemoteServerModel::SQL_EXECUTE)
                    ->customFormat(function () use ($k) {
                        return unserialize($this->data)[$k] ?? null;
                    });
                }
            });
            $form->html('<div class="alert alert-info">
            '.admin_trans('remote-server.you_can_use').'
            <li><code>{$title}</code> '.admin_trans('order.fields.title').'</li>
            <li><code>{$gd_name}</code> '.admin_trans('goods.fields.gd_name').'</li>
            <li><code>{$gd_id}</code> '.admin_trans('goods.fields.gd_id').'</li>
            <li><code>{$order_sn}</code> '.admin_trans('order.fields.order_sn').'</li>
            <li><code>{$email}</code> '.admin_trans('order.fields.email').'</li>
            <li><code>{$price}</code> '.admin_trans('order.fields.total_price').'</li>
            <li><code>{$'.admin_trans('remote-server.labels.uid').'}</code> '.admin_trans('remote-server.helps.uid').'</li>
            </div>', $label = '');
            $form->display('created_at');
            $form->display('updated_at');
            $form->hidden('data');
            $form->saving(function (Form $form) {
                $data = $form->input();
                unset($data['name'], $data['type'], $data['_token'], $data['data']);
                $form->data = serialize($data);
                foreach (array_keys($data) as $k => $v)
                    $form->deleteInput($v);
            });
        });
    }
}
