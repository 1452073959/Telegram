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
            $grid->model()->orderBy('balance', 'desc');
            $grid->column('id')->sortable();
//            $grid->column('chat_ground_id');
            $grid->column('user_no');
            $grid->column('user_name');
//            $grid->column('add_time');
            $grid->column('balance');
//            $grid->column('user_status');
//            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            // 禁用详情按钮
            $grid->disableViewButton();
            // 禁用编辑按钮
            $grid->disableEditButton();
            // 显示快捷编辑按钮
            $grid->showQuickEditButton();
            // 禁用批量删除按钮
            $grid->disableBatchDelete();
            // 禁用创建按钮
            $grid->disableCreateButton();
            // 禁用行选择器
            $grid->disableRowSelector();
            $grid->export()->csv();//导出
            $grid->filter(function (Grid\Filter $filter) {
                // 在这里添加字段过滤器
                $filter->equal('user_no' );
                $filter->like('user_name');
        
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
//            $form->display('id');
            $form->text('user_no');
            $form->text('balance');
//            $form->display('created_at');
//            $form->display('updated_at');
        });
    }
}
