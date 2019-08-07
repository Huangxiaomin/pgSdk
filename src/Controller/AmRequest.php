<?php
/**
 * Created by PhpStorm.
 * User: huangxm
 * Date: 2019-08-07
 * Time: 11:43
 */

namespace Paas;

class AmRequest
{
    /**
     * 根据unionId 查询会员信息
     *
     * @var string
     */
    const QUERY_UNIONID_URL = '%s/cms-account-micro-service/AccountServiceRest/queryUnionId';

    /**
     * 更具unionId 查询会员信息
     *
     *
     * @return string
     *
     * @date   2019-08-07 14:11
     */
    public function queryUnionId()
    {
        return self::QUERY_UNIONID_URL;
    }
}
