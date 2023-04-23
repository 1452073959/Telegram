<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\TelegramHistory;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class TelegramHistoryController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new TelegramHistory(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
//            $grid->column('chat_ground_id');
            $grid->column('user_no');
            $grid->column('user_name');
            $grid->column('send_time');
            $grid->column('send_text');
// 禁用操作按钮
            $grid->disableActions();
            // 禁用创建按钮
            $grid->disableCreateButton();
            // 禁用行选择器
            $grid->disableRowSelector();
            $grid->addTableClass(['table-text-center']);//表格文字居中
            // 开启字段选择器功能
            $grid->showColumnSelector();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_no' );
                $filter->like('user_name');
                $filter->like('send_text');

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
        return Show::make($id, new TelegramHistory(), function (Show $show) {
            $show->field('id');
            $show->field('chat_ground_id');
            $show->field('user_no');
            $show->field('user_name');
            $show->field('send_time');
            $show->field('send_text');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new TelegramHistory(), function (Form $form) {
            $form->display('id');
            $form->text('chat_ground_id');
            $form->text('user_no');
            $form->text('user_name');
            $form->text('send_time');
            $form->text('send_text');
        });
    }
}
