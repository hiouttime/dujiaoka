<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\BatchRestore;
use App\Admin\Actions\Post\Restore;
use App\Admin\Repositories\Goods;
use App\Models\Carmis;
use App\Models\Coupon;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\Pay as PayModel;
use App\Models\Goods as GoodsModel;
use App\Models\GoodsGroup as GoodsGroupModel;
use App\Models\RemoteServer as RemoteServerModel;

class GoodsController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Goods(['group', 'coupon']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'DESC');
            $grid->column('id')->sortable();
            $grid->column('picture')->image('', 100, 100);
            $grid->column('gd_name');
            $grid->column('group.gp_name', admin_trans('goods.fields.group_id'));
            $grid->column('type')
                ->using(GoodsModel::getGoodsTypeMap())
                ->label([
                    GoodsModel::AUTOMATIC_DELIVERY => Admin::color()->success(),
                    GoodsModel::MANUAL_PROCESSING => Admin::color()->info(),
                    GoodsModel::AUTOMATIC_PROCESSING => Admin::color()->warning(),
                ]);
            $grid->column('price')->sortable();
            $grid->column('stock')->display(function () {
                // 如果为自动发货，则加载库存卡密
                if ($this->type == GoodsModel::AUTOMATIC_DELIVERY) {
                    return Carmis::query()->where('goods_id', $this->id)
                        ->where('status', Carmis::STATUS_UNSOLD)
                        ->count();
                } else {
                    return $this->in_stock;
                }
            });
            $grid->column('sales_volume');
            $grid->column('ord')->editable()->sortable();
            $grid->column('is_open')->switch();
            $grid->column('updated_at');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('gd_name');
                $filter->equal('type')->select(GoodsModel::getGoodsTypeMap());
                $filter->equal('group_id')->select(GoodsGroupModel::query()->pluck('gp_name', 'id'));
                $filter->scope(admin_trans('dujiaoka.trashed'))->onlyTrashed();
                $filter->equal('coupon.coupons_id', admin_trans('goods.fields.coupon_id'))->select(
                    Coupon::query()->pluck('coupon', 'id')
                );
            });
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if (request('_scope_') == admin_trans('dujiaoka.trashed')) {
                    $actions->append(new Restore(GoodsModel::class));
                }
            });
            $grid->batchActions(function (Grid\Tools\BatchActions $batch) {
                if (request('_scope_') == admin_trans('dujiaoka.trashed')) {
                    $batch->add(new BatchRestore(GoodsModel::class));
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
        return Show::make($id, new Goods(), function (Show $show) {
            $show->id('id');
            $show->field('gd_name');
            $show->field('gd_description');
            $show->field('gd_keywords');
            $show->field('picture')->image();
            $show->field('price');
            $show->field('stock');
            $show->field('ord');
            $show->field('sales_volume');
            $show->field('type')->as(function ($type) {
                return admin_trans('goods.fields.'.strtolower($type));
            });
            $show->field('preselection');
            $show->field('is_open')->as(function ($isOpen) {
                if ($isOpen == GoodsGroupModel::STATUS_OPEN) {
                    return admin_trans('dujiaoka.status_open');
                } else {
                    return admin_trans('dujiaoka.status_close');
                }
            });
            $show->wholesale_price_cnf()->unescape()->as(function ($wholesalePriceCnf) {
                return  "<textarea class=\"form-control field_wholesale_price_cnf _normal_\"  rows=\"10\" cols=\"30\">" . $wholesalePriceCnf . "</textarea>";
            });
            $show->other_ipu_cnf()->unescape()->as(function ($otherIpuCnf) {
                return  "<textarea class=\"form-control field_wholesale_price_cnf _normal_\"  rows=\"10\" cols=\"30\">" . $otherIpuCnf . "</textarea>";
            });
            $show->field('api_hook');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $repository = new Goods(['goods_sub']);
        return Form::make($repository, function (Form $form) {
            $form->display('id');
            $form->text('gd_name')->required();
            $form->text('gd_description')->required();
            $form->text('gd_keywords')->required();
            $form->select('group_id')->options(
                GoodsGroupModel::query()->pluck('gp_name', 'id')
            )->required();
            $form->image('picture')->autoUpload()->uniqueName()->help(admin_trans('goods.helps.picture'));
            $form->url('picture_url')->help(admin_trans('goods.helps.picture_url'));
            $form->saving(function (Form $form) {
                if($form->picture_url)
                    $form->picture = $form->picture_url;
                $form->deleteInput('picture_url');
            });
            $form->radio('type')->options(GoodsModel::getGoodsTypeMap())->default(GoodsModel::AUTOMATIC_DELIVERY);
            $form->radio('is_sub')
            ->options([
                GoodsModel::STATUS_CLOSE => admin_trans('goods.options.not_sub'),
                GoodsModel::STATUS_OPEN => admin_trans('goods.options.is_sub')
             ])->default(GoodsModel::STATUS_CLOSE)
             ->when(GoodsModel::STATUS_CLOSE, function (Form $form) { 
                $form->currency('price')->default(0)->required();
                $form->number('stock')->help(admin_trans('goods.helps.in_stock'));
                $form->number('sales_volume');
            })->when(GoodsModel::STATUS_OPEN, function (Form $form) { 
                $form->hasMany('goods_sub', function (Form\NestedForm $form) {
                    $form->text('name')->required();
                    $form->currency('price')->default(0)->required();
                    $form->number('stock')->help(admin_trans('goods.helps.in_stock'));
                    $form->number('sales_volume');
                })->useTable();
            });
            $form->multipleSelect('payment_limit')
            ->options(PayModel::where('is_open', PayModel::STATUS_OPEN)->pluck('pay_name', 'id')->toArray())
            ->saving(function ($v) {return json_encode($v);})
            ->help(admin_trans('goods.helps.payment_limit'));
            $form->number('buy_limit_num')->help(admin_trans('goods.helps.buy_limit_num'));
            $form->currency('preselection')->default(0)->help(admin_trans('goods.helps.preselection'));
            $form->editor('buy_prompt');
            $form->editor('description');
            $form->textarea('other_ipu_cnf')->help(admin_trans('goods.helps.other_ipu_cnf'));
            $form->textarea('wholesale_price_cnf')->help(admin_trans('goods.helps.wholesale_price_cnf'));
            $form->select('api_hook')
                ->help(admin_trans('remote-server.helps.goods'))
                ->options(function () {
                    $servers = RemoteServerModel::all()->pluck('name', 'id');
                    return $servers->toArray();
                })->saving(function ($value) {
                    return $value == 0 ? null : $value;
            });
            $form->number('ord')->default(1)->help(admin_trans('dujiaoka.ord'));
            $form->switch('is_open')->default(GoodsModel::STATUS_OPEN);
        });
    }
    
     /**
     * 获得商品子规格列表
     *
     * @param Request $request
     * @return Content
     *
     * @author    assimon<ashang@utf8.hk>
     * @copyright assimon<ashang@utf8.hk>
     * @link      http://utf8.hk/
     */
    public function getGoodsSub(\Illuminate\Http\Request $request)
    {
        $goods_id = $request->get('q');
        $goods = GoodsModel::find($goods_id);
        $subs = [];
        if ($goods->is_sub == GoodsModel::STATUS_OPEN) {
            $subs = GoodsModel::with(['goods_sub' => function($query) {
                $query->select('id', 'goods_id', \Illuminate\Support\Facades\DB::raw('name as text'));
            }])->find($goods_id);
        }
        
        if ($subs) {
            return response()->json($subs->goods_sub);
        } else {
            return response()->json([['id'=>0, 'text'=>admin_trans('carmis.options.non_sub')]]);
        }
    }
}
