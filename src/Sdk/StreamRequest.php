<?php
/**
 * Created by PhpStorm.
 * User: huangxm
 * Date: 2019-08-07
 * Time: 15:14
 */

namespace Paas\Sdk;

use Paas\Kernel\Application;

class StreamRequest extends Application
{
    protected $serviceName = 'stream-api';

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * 推送消息
     */
    const MESSAGE = '%s/stream-api/message/{eventTypeId}';

    /**
     * 向EventHub推送消息
     *
     * @param array $data
     * @param $eventTypeId
     *
     * @throws \Exception
     * @return array
     *
     * @date   2019-08-08 11:09
     */
    public function message(array $data, $eventTypeId)
    {
        $url = str_replace('{eventTypeId}', $eventTypeId, self::MESSAGE);
        return $this->httpPost($url, $data);
    }
}
