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
    const MESSAGE = '/stream-api/message/{eventTypeId}';

    /**
     * 向EventHub推送消息
     *
     * @param array $data
     *
     * @return string
     *
     * @date   2019-08-07 15:17
     */
    public function message(array $data)
    {
        $url = self::MESSAGE;
        foreach ($data as $fields => $value) {
            $url = str_replace('{'.$fields.'}', $value, $url);
        }

        return $this->httpPost($url, $data);
    }
}
