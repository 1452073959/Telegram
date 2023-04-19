<?php

namespace App\Http\Controllers;

use App\Jobs\Closeorder;
use App\Jobs\RemoveGrounp;
use App\Models\TelegramHistory;
use App\Models\TelegramOrder;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;

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
        //創建订单
        $order=new TelegramOrder();
        $order->no=date('YmdHis'.time()).rand(1000,9999);//订单号
        $order->u_money=20.03;//订单号
        $order->user_id=33;//订单号
        $order->save();
        die;
        return json_encode($arr, true);
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

    public function start(Request $request)
    {
        $messageall = $request->all();
        Log::info($messageall);
        dump(isset($messageall['message']));
        dump(isset($messageall['callback_query']));
        if (isset($messageall['message'])) {
            $update = $messageall['message'];
            Log::info($update);
            $text = $update['text'];
            $chatId = $update['chat']['id'];
            $name = $update['from']['first_name'];
            $history = new TelegramHistory();
            $history->chat_ground_id = $chatId;
            $history->user_no = $chatId;
            $history->user_name = $name;
            $history->send_time = date('Y-m-d H:i:s', time());
            $history->send_text = $text;
            $history->save();

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
频道：【选填/没频道可以不填】'
                    ]);

                    return 'ok';
                    break;


                case '个人中心👤':

                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "用户ID:$chatId
姓氏: $name
用户名: 
USDT余额:"
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
                default:
                    //判断是否存在
                    $user = TelegramUser::where('user_no', $chatId)->first();
                    if (!$user) {
                        //开始的时候,重启
                        $user = new TelegramUser();
                        $user->user_no = $chatId;
                        $user->user_name = $name;
                        $user->chat_ground_id = $chatId;
                        $user->add_time = date('Y-m-d H:i:s', time());
                        $user->user_status = '3';
                        $user->save();
                    }

                    // 定义五个自定义内容
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
                    break;
            }
        } elseif (isset($messageall['callback_query'])) {
            $callback_query = $messageall['callback_query'];
            Log::info($callback_query);
            $message = Cache::get('cz' . $callback_query['message']['message_id']);//获取消息回调的id
            if ($message) {
                $callback_query_data = $callback_query['data'];//选择的值
                //添加两位随机小数
                $callback_query_data+=(rand(10,99)/100);
                $chatId = $callback_query['from']['id'];
                $date = date('Y-m-d H:i:s', time());
                if ($callback_query_data) {
                    // 发送回复消息
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "
注意小数点：$callback_query_data USDT 转错金额不认
注意小数点：$callback_query_data 转错金额不认
注意小数点：$callback_query_data 转错金额不认

转账  $callback_query_data 转账地址 TTa7W4EoEVES3sF111h338U4En5p3bkEgV（点击即可复制）

充值时间：$date
请在60分钟完成付款，转错不认。
收款地址为 USDT-TRC20
转账10分钟后没到账及时联系>>"
                    ]);
                    //获取充值用户,
                    $user = TelegramUser::where('user_no', $chatId)->first();
                    //判断订幂等
//                    是否有相同金额并且状态是未完成
                    $umoney=TelegramOrder::where('u_money',$callback_query_data)
                        ->where('orser_status',1)
                        ->first();
                    if($user&&$umoney){
                        //創建订单
                        $order=new TelegramOrder();
                        $order->no=date('YmdHis'.time()).rand(1000,9999);//订单号
                        $order->u_money=$callback_query_data;//金额
                        $order->user_id = $user['id'];//用户
                        $order->save();
                        //60分钟未支付
                        Closeorder::dispatch($order)->delay(now()->addMinutes(1));
                    }else{
                        //失败的时候
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => '订单创建失败!请重试'
                        ]);
                    }

                }
            }

        } else {
            return 'ok,其他';
        }


//        $chatId = '5815318219'; // 群组的 chat_id

    }


}
