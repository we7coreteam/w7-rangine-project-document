<?php
namespace W7\App\Model\Service\Api;

use GuzzleHttp\Client;

class ApiBase
{
    private $client = null;
    protected $domain = '';

    public function getClient()
    {
        if(!$this->client){
            $this->client = new Client([
                'timeout' => 5.0,
            ]);
        }
        return $this->client;
    }

    public function get($url,$params=[])
    {
        $url = $this->domain.$url;
        \ilogger()->info('api-request', ['url'=>$url,'params'=>$params]);
        $response = $this->getClient()->get($url,[
           'query' => $params
        ]);
        $contents = $response->getBody()->getContents();
        \ilogger()->info('api-response', ['url'=>$url,'body'=>$contents,'header'=>$response->getHeaders()]);
        return $contents;
    }

    public function postJson($url,$params=[])
    {
        $url = $this->domain.$url;
//        $options = json_encode($params,JSON_UNESCAPED_UNICODE);
//        $data = [
//            'body' => $options,
//            'headers' => ['content-type' => 'application/json']
//        ];
        \ilogger()->info('api-request', ['url'=>$url,'params'=>$params]);
        $response = $this->getClient()->request('POST',$url,[
            'json' => $params,
        ]);
        $contents = $response->getBody()->getContents();
        \ilogger()->info('api-response', ['url'=>$url,'body'=>$contents,'header'=>$response->getHeaders()]);
        return $contents;
    }

    public function post($url,$params=[])
    {
        $url = $this->domain.$url;
        \ilogger()->info('api-request', ['url'=>$url,'params'=>$params]);
        $response = $this->getClient()->request('POST',$url,[
            'form_params' => $params,
//            'headers' => ['content-type' => 'application/x-www-form-urlencoded']
        ]);
        $contents = $response->getBody()->getContents();
        \ilogger()->info('api-response', ['url'=>$url,'body'=>$contents,'header'=>$response->getHeaders()]);
        return $contents;
    }



}
