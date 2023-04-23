<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Examples\NewUsers;
use App\Admin\Repositories\TelegramOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Metrics\Card;

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
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('order_status', [1 => '已支付', 2 => '未支付',3=>'关闭']);
            });
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            // 关联 profile 表数据
            $grid->model()->with(['user']);
            $grid->column('user.user_no','用户id');
            $grid->column('user.user_name','用户名');
            $grid->column('no');
            $grid->column('u_money');
            $grid->column('payment_time');
            $grid->column('order_hash');
            $grid->column('order_address');
            $grid->order_status->using([1 => '已支付', 2 => '未支付',3=>'关闭']);
//            $grid->column('order_createtime');
            $grid->column('order_updatetime');
            // 禁用操作按钮
            $grid->disableActions();
            // 禁用创建按钮
            $grid->disableCreateButton();
            // 禁用行选择器
            $grid->disableRowSelector();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('user.user_no','用户id');
                $filter->like('user.user_name','用户名');
                $filter->between('order_updatetime')->datetime();
            });
            $grid->addTableClass(['table-text-center']);//表格文字居中
            // 开启字段选择器功能
            $grid->showColumnSelector();

            $grid->footer(function ($collection) use ($grid) {
                $query = \App\Models\TelegramOrder::query();
                // 拿到表格筛选 where 条件数组进行遍历
                $grid->model()->getQueries()->unique()->each(function ($value) use (&$query) {
                    if (in_array($value['method'], ['paginate', 'get', 'orderBy', 'orderByDesc'], true)) {
                        return;
                    }

                    $query = call_user_func_array([$query, $value['method']], $value['arguments'] ?? []);
                });

                // 查出统计数据
                $data = $query->sum('u_money');
                return "<div style='padding: 10px;'>总收入 ： $data</div>";
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
