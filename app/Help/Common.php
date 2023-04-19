<?php
function Tojson($data)
{
    return json_encode($data,true);
}

function http_post_data($url, $data_string="")
{
    $data_string = json_encode($data_string);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

    $ssl = preg_match('/^https:\/\//i', $url) ? TRUE : FALSE;

    curl_setopt($ch, CURLOPT_URL, $url);

    if ($ssl) {

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在

    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data_string))
    );

    //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/

    //curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);

    curl_setopt($ch, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/


    curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    ob_start();
    curl_exec($ch);
    $return_content = ob_get_contents();
    ob_end_clean();
    $return_content = json_decode($return_content, true);
    $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return array($return_code, $return_content);
}


if (!function_exists('json')) {
    /**
     * 获取\think\response\Json对象实例
     * @param mixed $data    返回的数据
     * @param int   $code    状态码
     * @param array $header  头部
     * @param array $options 参数
     * @return \think\response\Json
     */
    function json($data = [])
    {
        return response()->json($data);
    }
}

if(!function_exists('test_function')) {
    function test_function()
    {
        return "自定义公共函数测试";
    }
}

