<?php

namespace App\Admin\Controllers;

use Illuminate\Support\Str;
use App\Admin\Repositories\Articles;
use App\Models\Articles as Article;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ArticleController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Articles(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('title');
            $grid->column('link');
            $grid->column('category');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
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
        return Show::make($id, new Articles(), function (Show $show) {
            $show->field('id');
            $show->field('title');
            $show->field('link');
            $show->field('content');
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
        return Form::make(new Articles(), function (Form $form) {
            $form->display('id')->required();
            $form->text('title')->required();
            $form->text('link')->help(admin_trans('article.helps.link'));
            $form->saving(function (Form $form) {
                if (empty($form->input('link')))
                     $form->input('link', Str::random(8));
            });
            $categories = Article::query()
                ->select('category')->distinct()
                ->pluck('category')
                ->filter()->values()->toArray();
            $form->autocomplete('category')->options($categories);
            $form->editor('content')->required();
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
