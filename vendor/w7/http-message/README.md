# we7HttpMessage

swoole request / response 的 psr 标准的实现

fork 自  Swoft Http-message 组件，感谢 Swoft 团队

# commit

ADD 下载文件　sendfile 

ADD 获取上传文件对象

FIXED response不能返回0

FIXED 优化整理代码

# use 

#### 初始化 Request Response对象
```php
$server = new \Swoole\Http\Server('0.0.0.0', 88, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

$server->on('request', function ($request, $response) {
	$psr7Request = \W7\Http\Message\Server\Request::loadFromSwooleRequest($request);
	$psr7Response = \W7\Http\Message\Server\Response::loadFromSwooleResponse($response);
	
	var_dump($psr7Request);
});

$server->start();
```

#### 获取post值

```php
$psr7Request->post('code');
```

#### 发送一个文件

 - $filePath 下载的文件物理路径
 - $startPos 需要分片下载时，指定文件的开始位置 
 - $chunkFileSize 需要分片下载时，每个分片的大小

```php
$psr7Response->withFile(new File($filePath, $startPos, $chunkFileSize));
```
