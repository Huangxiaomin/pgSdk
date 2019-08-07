<?php
/**
 * Created by PhpStorm.
 * User: huangxm
 * Date: 2019-08-07
 * Time: 10:54
 */

namespace Paas;

use Paas\Kernel\PaasBase;

class Application extends PaasBase
{
    /**
     * 配置信息
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
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
     *  发送请求
     *
     * @param $function
     * @param $data
     *
     * @return array
     * @author huangxiaomin <huangxiaomin@vchangyi.com>
     *
     * @date   2019-08-07 15:37
     */
    public function send($function, $data)
    {
        $className = $this->classAlias();
        $classObject = new $className;

       $url = call_user_method($function, $classObject, $data);

       return $this->httpPost($url, $data);
    }

    /**
     * 类的别名
     *
     * @return array
     *
     * @date   2019-08-07 15:41
     */
    private function classAlias()
    {
        $alias = [
            'stream-api' => 'StreamRequest',
            'cms-account-micro-service' => 'AmRequest'
        ];

        return $alias[$this->serviceName];
    }
}
