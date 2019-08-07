<?php
/**
 * Created by PhpStorm.
 * User: huangxm
 * Date: 2019-08-07
 * Time: 11:43
 */

namespace Paas;



class AmRequest extends Application
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
     * @param $requestData
     *
     * @return array
     * @author huangxiaomin <huangxiaomin@vchangyi.com>
     *
     * @date   2019-08-07 14:11
     */
    public function queryUnionId($requestData)
    {
        return $this->httpPost(self::QUERY_UNIONID_URL, $requestData);
    }
}
