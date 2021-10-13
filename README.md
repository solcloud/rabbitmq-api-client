# RabbitMQ api client

```php
use Solcloud\RabbitMQ\Api\Client;
use Solcloud\Http\Contract\IRequestDownloader;
use Solcloud\Http\Request;

/** @var IRequestDownloader $downloader */
$downloader = new \Solcloud\Curl\CurlRequest(); // for example solcloud/curl package
$request = new Request();

$rabbitmqApiClient = new Client($downloader, $request);
$rabbitmqApiClient->setApiUrl('http://rabbitmq-api');
$rabbitmqApiClient->setVhost('vhost');
$rabbitmqApiClient->setApiUsername('username');
$rabbitmqApiClient->setApiPassword('password');

echo $rabbitmqApiClient->getTotalMessageCountInQueue('queue-name');
```
