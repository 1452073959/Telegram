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
//         dump($input);die;
        $default = TelegramSetting::first();
        $default->name = $input['name'];

        $default->describe = $input['describe'];
        $default->u_address = $input['u_address'];
        $default->publish_channel = $input['publish_channel'];
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
        $this->text('publish_channel', '发布频道');
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
