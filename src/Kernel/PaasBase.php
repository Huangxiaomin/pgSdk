<?php
/**
 * Created by PhpStorm.
 * User: huangxm
 * Date: 2019-08-07
 * Time: 11:53
 */

namespace Paas\Kernel;


abstract class PaasBase
{
    /**
     * 服务名字
     *
     * @var string
     */
    protected $serviceName = '';

    /**
     * 发送请求
     *
     * @param $url
     * @param $requestData
     *
     * @return array
     * @author huangxiaomin <huangxiaomin@vchangyi.com>
     *
     * @date   2019-08-07 14:46
     */
    public function httpPost($url, $requestData)
    {
        $requestData['sign'] = $this->makeSign($requestData);
        $data     = \GuzzleHttp\json_encode($requestData);
        $client   = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->config['base_uri'],
            // You can set any number of default request options.
            'timeout'  => 10,
        ]);
        $response = $client->post($url, [
            'headers' => [
                'Content-Type'              => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->config[$this->serviceName]
            ],
            'body'    => $data
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 获取签名
     *
     * @param $requestData
     *
     * @return string
     * @author huangxiaomin <huangxiaomin@vchangyi.com>
     *
     * @date   2019-08-07 14:35
     */
    private function makeSign($requestData)
    {
        // 将请求 BODY JSON 化
        $jsonRequestData = json_encode(empty($requestData) ? (object)$requestData : $requestData, 320);

        $params = [
            'api_key'   => $this->config['api_key'],
            'body'      => $jsonRequestData,
            'nonce_str' => md5(uniqid(microtime(true), true)),
            'timestamp' => date(time(), 'Y-m-d H:i:s'),
        ];

        // 按照字典排序
        ksort($params, SORT_STRING);
        // 以 & 链接并且前后拼接 secret
        $tmp = $this->config['secret'] . urldecode(http_build_query($params)) . $this->config['secret'];
        // SHA 256 加密后，全部转大写
        return strtoupper(hash('sha256', $tmp));
    }
}
