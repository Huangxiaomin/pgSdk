<?php
/**
 * Created by PhpStorm.
 * User: huangxm
 * Date: 2019-08-07
 * Time: 10:54
 */

namespace Paas\Kernel;

use GuzzleHttp\Client;

class Application extends PaasBase
{
    /**
     * 配置信息
     *
     * @var array
     */
    protected $config = [];

    protected $serviceName = '';

    public function __construct(array $config)
    {
        $this->checkConfig($config);
        $this->config      = $config;
    }

    /**
     * 验证配置是否正确
     *
     * @param $config
     *
     * @throws \Exception
     * @return void
     *
     * @date   2019-08-07 11:47
     */
    private function checkConfig(array $config)
    {
        if (empty($config)) {
            throw new \Exception('config is empty');
        }

        if (!isset($config['base_uri']) || empty($config['base_uri'])) {
            throw new \Exception('base uri is empty');
        }

        if (!isset($config['api_key']) || empty($config['api_key'])) {
            throw new \Exception('api_key is empty');
        }

        if (!isset($config['secret']) || empty($config['secret'])) {
            throw new \Exception('secret is empty');
        }
    }

    /**
     * 生成签名
     *
     * @param $params
     *
     * @return string
     *
     * @date   2019-08-07 17:21
     */
    private function makeSign($params)
    {
        // 按照字典排序
        ksort($params, SORT_STRING);
        // 以 & 链接并且前后拼接 secret
        $tmp = $this->config['secret'] . urldecode(http_build_query($params)) . $this->config['secret'];
        // SHA 256 加密后，全部转大写
        return strtoupper(hash('sha256', $tmp));

    }

    /**
     * 组装请求参数
     *
     * @param $requestData
     *
     * @return string
     *
     * @date   2019-08-07 19:05
     */
    private function mergeRequestData($requestData)
    {
        // 将请求 BODY JSON 化
        $jsonRequestData = json_encode(empty($requestData) ? (object)$requestData : $requestData, 320);
        $params = [
            'api_key'   => $this->config['api_key'],
            'body'      => $jsonRequestData,
            'nonce_str' => md5(uniqid(microtime(true), true)),
            'timestamp' => date('Y-m-d H:i:s', time()),
        ];
        $params['sign'] = $this->makeSign($params);
        unset($params['body']);
        // 组装查询参数
        return http_build_query($params);
    }

    /**
     * 描述
     *
     * @param $url
     * @param $requestData
     *
     * @throws \Exception
     * @return array
     *
     * @date   2019-08-08 11:07
     */
    public function httpPost($url, $requestData)
    {
        $data                = \GuzzleHttp\json_encode($requestData);
        try {
            $url .= '?' . $this->mergeRequestData($requestData);
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->config['base_uri'],
                // You can set any number of default request options.
                'timeout'  => 10,
            ]);

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type'              => 'application/json',
                    'Ocp-Apim-Subscription-Key' => $this->config['service_name'][$this->serviceName]['subscription_key']
                ],
                'body'    => $data
            ]);
            $result     = json_decode($response->getBody()->getContents(), true);

            if ($result['resultCode'] != 0) {
                throw new \Exception($result['message']);
            }
            return $result['object'];
        } catch (\Exception $exception) {
            throw new \Exception($this->response($exception->getCode()));
        }
    }

    /**
     * 描述
     *
     * @param $code
     *
     * @throws \Exception
     * @return void
     * @author huangxiaomin <huangxiaomin@vchangyi.com>
     *
     * @date   2019-08-07 18:43
     */
    private function response($code)
    {
        $message  = '接口调用出错';
        $response = [];
        switch ($code) {
            case 201:
                $message = '已经创建';
            case 401:
                $message = '接口请求无权限';
            case 403:
            case 404:
                break;
            default:
                break;
        }
        return $message;
    }
}
