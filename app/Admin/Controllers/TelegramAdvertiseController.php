<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\TelegramAdvertise;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class TelegramAdvertiseController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new TelegramAdvertise(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('advertise_content');
            $grid->column('send_time');
            $grid->column('user_id');
            $grid->column('deduction_money');
            $grid->column('send_channel');
            $grid->column('advertise_createtime');
            $grid->column('advertise_updatetime');
        
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
        return Show::make($id, new TelegramAdvertise(), function (Show $show) {
            $show->field('id');
            $show->field('advertise_content');
            $show->field('send_time');
            $show->field('user_id');
            $show->field('deduction_money');
            $show->field('send_channel');
            $show->field('advertise_createtime');
            $show->field('advertise_updatetime');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new TelegramAdvertise(), function (Form $form) {
            $form->display('id');
            $form->text('advertise_content');
            $form->text('send_time');
            $form->text('user_id');
            $form->text('deduction_money');
            $form->text('send_channel');
            $form->text('advertise_createtime');
            $form->text('advertise_updatetime');
        });
    }
}
