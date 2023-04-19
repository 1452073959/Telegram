<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\TelegramOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class TelegramOrderController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new TelegramOrder(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('no');
            $grid->column('u_money');
            $grid->column('payment_time');
            $grid->column('order_hash');
            $grid->column('order_address');
            $grid->column('order_createtime');
            $grid->column('order_updatetime');
        
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
        return Show::make($id, new TelegramOrder(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('no');
            $show->field('u_money');
            $show->field('payment_time');
            $show->field('order_hash');
            $show->field('order_address');
            $show->field('order_createtime');
            $show->field('order_updatetime');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new TelegramOrder(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('no');
            $form->text('u_money');
            $form->text('payment_time');
            $form->text('order_hash');
            $form->text('order_address');
            $form->text('order_createtime');
            $form->text('order_updatetime');
        });
    }
}
