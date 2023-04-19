<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\TelegramUser;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class TelegramUserController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new TelegramUser(), function (Grid $grid) {
            $grid->column('id')->sortable();
//            $grid->column('chat_ground_id');
            $grid->column('user_no');
            $grid->column('user_name');
//            $grid->column('add_time');
            $grid->column('balance');
//            $grid->column('user_status');
//            $grid->column('created_at');
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
        return Show::make($id, new TelegramUser(), function (Show $show) {
            $show->field('id');
            $show->field('chat_ground_id');
            $show->field('user_no');
            $show->field('user_name');
            $show->field('add_time');
            $show->field('balance');
            $show->field('user_status');
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
        return Form::make(new TelegramUser(), function (Form $form) {
            $form->display('id');
            $form->text('chat_ground_id');
            $form->text('user_no');
            $form->text('user_name');
            $form->text('add_time');
            $form->text('balance');
            $form->text('user_status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
