<?php

namespace App\Admin\Forms;

use App\Models\TelegramSetting;
use Dcat\Admin\Widgets\Form;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;

class Setting extends Form
{


    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {

// 使用字符串处理函数提取频道 ID
        $channelId = substr($input['publish_channel'], strrpos($input['publish_channel'], '/') + 1);

// 检查提取到的频道 ID 是否为空
        if (!empty($channelId)) {
            // 使用 $channelId 变量作为频道 ID 进行后续操作
            // 例如将 $channelId 作为参数传递给 Telegram Bot API 中的相关方法

            $default = TelegramSetting::first();
            $default->name = $input['name'];
            $default->describe = $input['describe'];
            $default->u_address = $input['u_address'];
            $default->publish_channel = '@'.$channelId;
            $default->advertise_price = $input['advertise_price'];
            $default->auditors = $input['auditors'];
            $default->save();
            //设置机器人名称
//        $setbotname=Telegram::setBotName($input['name']);
//        dump($setbotname);
            if (!$default) {
                return $this->response()->error('Your error message.');
            }
            return $this
                ->response()
                ->success('Processed successfully.')
                ->refresh();
        } else {
            // 频道链接无效，处理错误
            return $this->response()->error('频道链接错误!.');
        }
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('name')->required();
//        $this->email('email')->rules('email');
        $this->text('describe', '描述');
        $this->text('u_address', '收U地址');
        $this->text('publish_channel', '发布频道')->help('复制链接可直接转换为频道id');;
        $this->text('advertise_price', '广告价格');
        $this->text('auditors', '审核人id');
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        //获取
        $default = TelegramSetting::first()->toarray();

        return $default;


    }
}
