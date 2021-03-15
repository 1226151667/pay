<?php
namespace Pay;
use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Config;

class Alipay implements PayInterface
{
    public function __construct($config)
    {
        Factory::setOptions($this->getOptions($config));
    }

    public function payOrder()
    {
        try {

            $result = Factory::payment()->common()->create("iPhone6 16G", "20200326235526001", "88.88", "2088002656718920");

            if (!empty($result->code) && $result->code === 10000) {
                return "调用成功". PHP_EOL;
            } else {
                return "调用失败，原因：". $result->msg."，".$result->sub_msg.PHP_EOL;
            }
        } catch (\Exception $e) {
            return "调用失败exception，". $e->getMessage(). PHP_EOL;
        }
    }

    private function getOptions($config)
    {
        $options = new Config();
        foreach ($config as $k => $v) {
            $options->{$k} = $v;
        }
        return $options;
    }
}
