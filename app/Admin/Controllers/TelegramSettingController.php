<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\TelegramSetting;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class TelegramSettingController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new TelegramSetting(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('describe');
            $grid->column('u_address');
            $grid->column('publish_channel');
            $grid->column('name');
        
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
        return Show::make($id, new TelegramSetting(), function (Show $show) {
            $show->field('id');
            $show->field('describe');
            $show->field('u_address');
            $show->field('publish_channel');
            $show->field('name');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new TelegramSetting(), function (Form $form) {
            $form->display('id');
            $form->text('describe');
            $form->text('u_address');
            $form->text('publish_channel');
            $form->text('name');
        });
    }
}
