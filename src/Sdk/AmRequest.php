<?php
/**
 * Created by PhpStorm.
 * User: huangxm
 * Date: 2019-08-07
 * Time: 11:43
 */

namespace Paas\Sdk;

use Paas\Kernel\Application;

class AmRequest extends Application
{
    protected $serviceName = 'cms-account-micro-service';

    /**
     * AmRequest constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * 根据unionId 查询会员信息
     *
     * @var string
     */
    const QUERY_UNIONID_URL = '/cms-account-micro-service/AccountServiceRest/queryUnionId';

    /**
     * 更具unionId 查询会员信息
     *
     *
     * @return string
     *
     * @date   2019-08-07 14:11
     */
    public function queryUnionId($data)
    {
        return $this->httpPost(self::QUERY_UNIONID_URL, $data);
    }
}
