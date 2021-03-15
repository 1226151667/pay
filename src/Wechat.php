<?php
namespace Pay;
use GuzzleHttp\Exception\RequestException;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;
use WechatPay\GuzzleMiddleware\Util\PemUtil;

class Wechat implements PayInterface
{
    public function payOrder()
    {
        // 商户相关配置
        $merchantId = '1000100'; // 商户号
        $merchantSerialNumber = 'XXXXXXXXXX'; // 商户API证书序列号
        $merchantPrivateKey = PemUtil::loadPrivateKey('/path/to/mch/private/key.pem'); // 商户私钥
        // 微信支付平台配置
        $wechatpayCertificate = PemUtil::loadCertificate('/path/to/wechatpay/cert.pem'); // 微信支付平台证书

        // 构造一个WechatPayMiddleware
        $wechatpayMiddleware = WechatPayMiddleware::builder()
            ->withMerchant($merchantId, $merchantSerialNumber, $merchantPrivateKey) // 传入商户相关配置
            ->withWechatPay([ $wechatpayCertificate ]) // 可传入多个微信支付平台证书，参数类型为array
            ->build();

        // 将WechatPayMiddleware添加到Guzzle的HandlerStack中
        $stack = GuzzleHttp\HandlerStack::create();
        $stack->push($wechatpayMiddleware, 'wechatpay');

        // 创建Guzzle HTTP Client时，将HandlerStack传入
        $client = new GuzzleHttp\Client(['handler' => $stack]);


        // 接下来，正常使用Guzzle发起API请求，WechatPayMiddleware会自动地处理签名和验签
        try {
            $resp = $client->request('GET', 'https://api.mch.weixin.qq.com/v3/...', [ // 注意替换为实际URL
                'headers' => [ 'Accept' => 'application/json' ]
            ]);

            echo $resp->getStatusCode().' '.$resp->getReasonPhrase()."\n";
            echo $resp->getBody()."\n";

            $resp = $client->request('POST', 'https://api.mch.weixin.qq.com/v3/...', [
                'json' => [ // JSON请求体
                    'field1' => 'value1',
                    'field2' => 'value2'
                ],
                'headers' => [ 'Accept' => 'application/json' ]
            ]);

            echo $resp->getStatusCode().' '.$resp->getReasonPhrase()."\n";
            echo $resp->getBody()."\n";
        } catch (RequestException $e) {
            // 进行错误处理
            echo $e->getMessage()."\n";
            if ($e->hasResponse()) {
                echo $e->getResponse()->getStatusCode().' '.$e->getResponse()->getReasonPhrase()."\n";
                echo $e->getResponse()->getBody();
            }
            return;
        }
    }
}
