<?php
/**
 * Created by PhpStorm.
 * User: huangxm
 * Date: 2019-08-07
 * Time: 10:54
 */

namespace Paas\Kernel;

class Application extends PaasBase
{
    /**
     * 配置信息
     *
     * @var array
     */
    protected $config = [];

    protected $serviceName = '';

    public function __construct(array $config, $serviceName)
    {
        $this->checkConfig($config, $serviceName);
        $this->config = $config;
        $this->serviceName = $serviceName;
    }

    /**
     * 验证配置是否正确
     *
     * @param $config
     *
     * @throws \Exception
     * @return void
     * @author huangxiaomin <huangxiaomin@vchangyi.com>
     *
     * @date   2019-08-07 11:47
     */
    private function checkConfig(array $config, $serviceName)
    {
        if (empty($config)) {
            throw new \Exception('config is empty');
        }

        if (isset($config['base_uri']) || empty($config['base_uri'])) {
            throw new \Exception('base uri is empty');
        }

        if (isset($config['api_key']) || empty($config['api_key'])) {
            throw new \Exception('api_key is empty');
        }

        if (isset($config['secret']) || empty($config['secret'])) {
            throw new \Exception('secret is empty');
        }

        if (isset($config['service_name']) || empty($config['service_name'])) {
            throw new \Exception('service_name is empty');
        }

        if (isset($config['service_name'][$serviceName]) || empty($config['service_name'][$serviceName])) {
            throw new \Exception('service_name is empty');
        }
    }

    /**
     * 描述
     *
     * @param $requestData
     *
     * @return string
     *
     * @date   2019-08-07 17:21
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

    /**
     * 描述
     *
     * @param $url
     * @param $requestData
     *
     * @return array|mixed
     *
     * @date   2019-08-07 17:21
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
                'Ocp-Apim-Subscription-Key' => $this->config[$this->serviceName]['subscription_key']
            ],
            'body'    => $data
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
