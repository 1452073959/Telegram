<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\Cancel;
use App\Admin\Actions\Grid\Send;
use App\Admin\Repositories\TelegramAdvertise;
use App\Models\TelegramUser;
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

            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('send_status', [1 => '待发送', 2 => '已发送',3=>'无效退回']);
            });

            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            // 关联 profile 表数据
            $grid->model()->with(['user']);
            $grid->column('user.user_no','用户id');
            $grid->column('user.user_name','用户名');
            $grid->column('advertise_content')->textarea();
            $grid->column('send_time')->display(function ($time) {
                return date('Y-m-d H:i',$time);

            });;
//            $grid->column('user_id');
            $grid->column('deduction_money');
            $grid->column('send_channel');
//            $grid->column('advertise_createtime');
            $grid->column('advertise_updatetime');
            $grid->column('refuse_describe','拒绝原因(备注)')->editable(true);
            $grid->send_status->using([1 => '待发送', 2 => '已发送',3=>'无效退回']);
// 也可以通过以下方式启用或禁用按钮
            $grid->disableDeleteButton();
            $grid->disableEditButton();
            $grid->disableQuickEditButton();
            $grid->disableViewButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {

                if($actions->row->send_status=='1'){
                    $actions->append(new Send());
                    $actions->append(new Cancel());
                }
            });
//            $grid->actions(new Send());
//            $grid->column('advertise_updatetime');
            $grid->footer(function ($collection) use ($grid) {
                $query = \App\Models\TelegramAdvertise::query();
                // 拿到表格筛选 where 条件数组进行遍历
                $grid->model()->getQueries()->unique()->each(function ($value) use (&$query) {
                    if (in_array($value['method'], ['paginate', 'get', 'orderBy', 'orderByDesc'], true)) {
                        return;
                    }

                    $query = call_user_func_array([$query, $value['method']], $value['arguments'] ?? []);
                });

                // 查出统计数据
                $data = $query->sum('deduction_money');
                return "<div style='padding: 10px;'>总计 ： $data</div>";
            });
            // 禁用创建按钮
            $grid->disableCreateButton();
            // 禁用行选择器
            $grid->disableRowSelector();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('user.user_no','用户id');
                $filter->like('user.user_name','用户名');
                $filter->like('advertise_content');
                $filter->between('advertise_createtime')->datetime();

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
            $form->text('refuse_describe');

            $form->text('advertise_createtime');
            $form->text('advertise_updatetime');
        });
    }
}
