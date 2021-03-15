<?php
namespace Pay;

class Pay
{
    private const CLASS_MAP = [
        'weixin' => 'Pay\Wechat',
        'alipay' => 'Pay\Alipay',
    ];
    private static $client = null;
    private static $type;
    private static $config;

    public static function getInstance($type, $config)
    {
        self::$type = $type;
        self::$config = $config;
        self::setClient();
        return self::getClient();
    }

    private static function setClient()
    {
        if (!is_null(self::$client)) {
            return;
        }
        if (!isset(self::CLASS_MAP[self::$type])) {
            throw new \Exception('不支持此支付方式...');
            return;
        }
        $class = self::CLASS_MAP[self::$type];
        self::$client = new $class(self::$config);
    }

    private static function getClient()
    {
        if (is_null(self::$client)) {
            self::setClient();
        }
        return self::$client;
    }
}
