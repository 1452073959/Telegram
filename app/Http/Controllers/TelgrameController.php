<?php

namespace App\Http\Controllers;

use App\Jobs\Closeorder;
use App\Jobs\RemoveGrounp;
use App\Models\TelegramAdvertise;
use App\Models\TelegramHistory;
use App\Models\TelegramOrder;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;
use App\Models\TelegramSetting;

class TelgrameController extends Controller
{
    //
    public function index(Request $request)
    {


        $message = $request->all();
        Log::info($message);
        $chatId = '-1001987792603'; // 群组的 chat_id
        //判断新用户加入
        if (isset($message['message']['new_chat_member'])) {
            $msg = $message['message']['new_chat_member'];
            // 当前用户是新加入的用户，进行相应的处理
            $username = ' @' . $msg['first_name']; // 要求回复的用户的用户名
            $rand = rand(1000, 9999);
            Cache::put($msg['id'], $rand, 60);
            $message = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => '请在60秒内输入以下内容,' . $rand . $username,
            ]);
            $user = new TelegramUser();
            $user->user_no = $msg['id'];
            $user->user_name = $msg['first_name'];
            $user->chat_ground_id = $chatId;
            $user->add_time = date('Y-m-d H:i:s', time());
            $user->user_status = '3';
            $user->save();
            RemoveGrounp::dispatch($user)
                ->delay(now()->addMinutes(1));

        }
        //记录聊天记录
        if (isset($message['message']['from']) && isset($message['message']['text'])) {
            $form = $message['message']['from'];
            $value = Cache::get($form['id']);//获取发言者的内容
            $history = new TelegramHistory();
            $history->chat_ground_id = $message['message']['chat']['id'];
            $history->user_no = $form['id'];
            $history->user_name = $form['first_name'];
            $history->send_time = date('Y-m-d H:i:s', time());
            $history->send_text = $message['message']['text'];
            $history->save();

            if ($message['message']['text'] == $value) {
                $res = TelegramUser::where('user_no', $form['id'])->where('user_status', '3')->first();

                if ($res) {
                    $res->user_status = '1';
                    $res->save();
                    $message = Telegram::sendMessage([
                        'chat_id' => $message['message']['chat']['id'],
                        'text' => '欢迎你!' . '@' . $form['first_name'],
                    ]);
                }

            }

        }


        return 'ok';

    }

    public function test()
    {




        $arr=array (
            'update_id' => 590437741,
            'message' =>
                array (
                    'message_id' => 988,
                    'from' =>
                        array (
                            'id' => 5815318219,
                            'is_bot' => false,
                            'first_name' => 'dudu',
                            'language_code' => 'zh-hans',
                        ),
                    'chat' =>
                        array (
                            'id' => 5815318219,
                            'first_name' => 'dudu',
                            'type' => 'private',
                        ),
                    'date' => 1682049051,
                    'text' => '消费记录📝',
                ),
        )  ;
        return json_encode($arr,true);





        $Advertise = TelegramAdvertise::where('user_id', 33)->limit(20)->get();
        foreach ($Advertise as $k => $v) {
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => '查看详情', 'callback_data' => $v['id']])
                );
            $str = '发送时间:' . date('Y-m-d H:i', $v['send_time']) .
                '扣费金额:' . $v['deduction_money'];
            if ($v['send_status'] == '3') {
                $str .= "状态:内容不合规,已退回!";
            } elseif ($v['send_status'] == '2') {
                $str .= "状态:已发送";
            } elseif ($v['send_status'] == '1') {
                $str .= "状态:待审核发送";
            }
            $response = Telegram::sendMessage([
                'chat_id' => 5815318219,
                'text' => $str,
                'reply_markup' => $keyboard,
                'chat_instance' => 'some_unique_id' // 设置 chat_instance 参数
            ]);
            Cache::put('cx' . $response['message_id'], '查询交易');

        }
        // 发送回复消息
        $a = Telegram::sendMessage([
            'chat_id' => -1001805255623,
            'text' => '付费广告发布规则如下
1:不得发布虚假诈骗广告，发现马上下架。

2:广告行数不能超过10行。

3:如在其他担保上了押金，开了公群，还要打广告的话请联系黄站长 @OPPO 多打20USDT发布，如果你不联系黄站长通过机器人发布广告，如果被发现将会直接下架你的广告并且余额清0'
        ]);
        dd($a);


    }

    //地址
    public function start(Request $request)
    {
        $messageall = $request->all();
        Log::info($messageall);
        dump(isset($messageall['message']));
        dump(isset($messageall['callback_query']));
        if (isset($messageall['message'])) {
            return $this->message($messageall);
        } elseif (isset($messageall['callback_query'])) {
            return $this->callback_query($messageall);

        } else {
            return 'ok,其他';
        }

    }


    //消息
    public function message($messageall)
    {
        $update = $messageall['message'];
        //如果是回复
        if (isset($update['reply_to_message'])) {
            return $this->reply_to_message($update);
        }
        Log::info($update);
        $text = $update['text'];
        $chatId = $update['from']['id'];
        $name = $update['from']['first_name'];
        //判断用户是否存在;
        $user = TelegramUser::where('user_no', $chatId)->first();
        $history = new TelegramHistory();
        $history->chat_ground_id = $chatId;
        $history->user_no = $chatId;
        $history->user_name = $name;
        $history->send_time = date('Y-m-d H:i:s', time());
        $history->send_text = $text;
        $history->save();

        //判断用户是否存在
        $user = TelegramUser::where('user_no', $chatId)->first();
        if (!$user) {
            //开始的时候,重启第一次访问
            $user = new TelegramUser();
            $user->user_no = $chatId;
            $user->user_name = $name;
            $user->chat_ground_id = $chatId;
            $user->add_time = date('Y-m-d H:i:s', time());
            $user->user_status = '3';
            $user->save();
            //发布键盘
            $custom_content_1 = '发布广告🔥';
            $custom_content_2 = '我要充值💰';
            $custom_content_3 = '个人中心👤';
            $custom_content_4 = '消费记录📝';
            $custom_content_5 = '👉必看发布规则👈';
            // 创建自定义键盘
            $keyboard = Keyboard::make([
                'keyboard' => [
                    [$custom_content_1],
                    [$custom_content_2],
                    [$custom_content_3],
                    [$custom_content_4],
                    [$custom_content_5],
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]);
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => '请选择一个选项：',
                'reply_markup' => $keyboard
            ]);
            return 'ok';

        }

        switch ($text) {
            case '👉必看发布规则👈':
                // 发送回复消息
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '付费广告发布规则如下

1:不得发布虚假诈骗广告，发现马上下架。

2:广告行数不能超过10行。

3:如在其他担保上了押金，开了公群，还要打广告的话请联系黄站长 @OPPO 多打20USDT发布，如果你不联系黄站长通过机器人发布广告，如果被发现将会直接下架你的广告并且余额清0'
                ]);
                return 'ok';
                break;
            case '发布广告🔥':

                // 发送回复消息
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '付费广告发布规则如下

1:不得发布虚假诈骗广告，发现马上下架。

2:广告行数不能超过10行。

3:如在其他担保上了押金，开了公群，还要打广告的话请联系黄站长 @OPPO 多打20USDT发布，如果你不联系黄站长通过机器人发布广告，如果被发现将会直接下架你的广告并且余额清0'
                ]);

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '项目名称：
项目介绍：
价格：
联系人：
请点击本消息选择回复,设置广告内容!'
                ]);

                return 'ok';
                break;
            case '个人中心👤':

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "用户ID:$chatId
用户名: $name
USDT余额: $user->balance"
                ]);
                return 'ok';
                break;
            case '我要充值💰':
                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        Keyboard::inlineButton(['text' => '100U', 'callback_data' => '100']),
                        Keyboard::inlineButton(['text' => '200U', 'callback_data' => '200']),
                        Keyboard::inlineButton(['text' => '400U', 'callback_data' => '400']),
                        Keyboard::inlineButton(['text' => '500U', 'callback_data' => '500'])
                    );

                // 发送回复消息，并附带 Inline Keyboard
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '请选择充值金额:',
                    'reply_markup' => $keyboard,
                    'chat_instance' => 'some_unique_id' // 设置 chat_instance 参数
                ]);
                Cache::put('cz' . $response['message_id'], '充值');
                return $response['message_id'];
                break;
            case '消费记录📝':
                $Advertise = TelegramAdvertise::where('user_id', $user['id'])->limit(20)->get();
                if (!$Advertise) {
                    $response = Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => '暂无消费记录!'
                    ]);
                    return $response['message_id'];
                    break;
                } else {
                 Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => '最近20条记录'
                    ]);
                    foreach ($Advertise as $k => $v) {
                        $keyboard = Keyboard::make()
                            ->inline()
                            ->row(
                                Keyboard::inlineButton(['text' => '查看详情', 'callback_data' => $v['id']])
                            );
                        $str = '发送时间:' . date('Y-m-d H:i', $v['send_time']) .
                            ';扣费金额:' . $v['deduction_money'];
                        if ($v['send_status'] == '3') {
                            $str .= ";状态:内容不合规,已退回!";
                        } elseif ($v['send_status'] == '2') {
                            $str .= ";状态:已发送";
                        } elseif ($v['send_status'] == '1') {
                            $str .= ";状态:待审核发送";
                        }
                        $response = Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => $str,
                            'reply_markup' => $keyboard,
                            'chat_instance' => 'some_unique_id' // 设置 chat_instance 参数
                        ]);
                        Cache::put('cx' . $response['message_id'], '查询交易');

                    }
                    return 'ok';
                    break;

                }

            case '/start':
                // 定义五个自定义内容
                $custom_content_1 = '发布广告🔥';
                $custom_content_2 = '我要充值💰';
                $custom_content_3 = '个人中心👤';
                $custom_content_4 = '消费记录📝';
                $custom_content_5 = '👉必看发布规则👈';
                // 创建自定义键盘
                $keyboard = Keyboard::make([
                    'keyboard' => [
                        [$custom_content_1, $custom_content_2],
                        [$custom_content_3, $custom_content_4],
                        [$custom_content_5],
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ]);

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '请选择一个选项：',
                    'reply_markup' => $keyboard
                ]);
                return 'ok';
                break;
            default:
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "未知内容;请输入 /start 重启机器人"
                ]);
                return $response['message_id'];


        }
    }

    //点击选项的回调,充值使用
    public function callback_query($messageall)
    {
        $callback_query = $messageall['callback_query'];
        $chatId = $callback_query['from']['id'];
        Log::info($callback_query);
        $message = Cache::get('cz' . $callback_query['message']['message_id']);//获取消息回调的id
        $cx = Cache::get('cx' . $callback_query['message']['message_id']);//获取消息回调的id查询id
        if ($message) {
            $callback_query_data = $callback_query['data'];//选择的值
            //添加两位随机小数
            $callback_query_data += (rand(10, 99) / 100);
            $date = date('Y-m-d H:i:s', time());
            if ($callback_query_data) {
                //广告费用配置信息
                $setting = TelegramSetting::first();
                // 发送回复消息
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "
注意小数点：$callback_query_data USDT 转错金额不认
注意小数点：$callback_query_data 转错金额不认
注意小数点：$callback_query_data 转错金额不认

转账  $callback_query_data 转账地址 $setting->u_address

充值时间：$date
请在60分钟完成付款，转错不认。
收款地址为 USDT-TRC20
转账10分钟后没到账及时联系>>"
                ]);
                //获取充值用户,
                $user = TelegramUser::where('user_no', $chatId)->first();
                //判断订幂等
//                    是否有相同金额并且状态是未完成
                $umoney = TelegramOrder::where('u_money', $callback_query_data)
                    ->where('order_status', '1')
                    ->first();
                if ($user && !$umoney) {
                    //創建订单
                    $order = new TelegramOrder();
                    $order->no = date('YmdHis' . time()) . rand(1000, 9999);//订单号
                    $order->u_money = $callback_query_data;//金额
                    $order->user_id = $user['id'];//用户
                    $order->save();
                    //60分钟未支付
                    Closeorder::dispatch($order)->delay(now()->addMinutes(1));
                    //订单创建成功
                    $response = Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => '订单创建成功!请在60分钟内完成付款!'
                    ]);
                } else {
                    //失败的时候
                    $response = Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => '订单创建失败!请重试'
                    ]);
                }

            }
        } elseif ($cx) {
            $cx_data = $callback_query['data'];//选择的值
            $Advertise = TelegramAdvertise::find($cx_data);
            $response = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $Advertise['advertise_content']
            ]);
        } else {
            $response = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "未识别回复内容!"
            ]);
        }
        return $response['message_id'];
    }

    //回复
    public function reply_to_message($update)
    {
        $text = $update['text'];//回复内容
        $chatId = $update['from']['id'];
        $name = $update['from']['first_name'];
        //用户信息
        $user = TelegramUser::where('user_no', $chatId)->first();
        //广告费用配置信息
        $setting = TelegramSetting::first();
        $reply = $update['reply_to_message']['text'];        //回复消息的内容
//        dump($reply);
        //截取回复内容前五个字确认回复内容
        $reply_substr = Str::limit($reply, 8, '');


        //回复的广告信息
        if ($reply_substr == "项目名称") {
            //判断余额是否足够
            if ($user['balance'] >= $setting['advertise_price']) {
                $advertise = new TelegramAdvertise();
                $advertise->advertise_content = $text;
                $advertise->send_time = time();
                $advertise->user_id = $user['id'];
                $advertise->deduction_money = $setting['advertise_price'];
                $advertise->send_channel = $setting['publish_channel'];
                $advertise->save();
                if ($advertise) {
                    $response = Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "成功,审核完成后将发布到到频道!"
                    ]);
                }
            } else {
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "余额不足,请先充值!"
                ]);
            }
        } else {
            $response = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "未识别回复内容!"
            ]);
        }
        return $response['message_id'];


    }


}
