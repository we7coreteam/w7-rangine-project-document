<?php
/**
 * @author donknap
 * @date 18-11-6 上午9:57
 */

namespace W7\App\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use W7\App;
use W7\Core\Cache\Cache;
use W7\Core\Middleware\MiddlewareAbstract;

class AdminMiddleware extends MiddlewareAbstract {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        //这里是中间件一些代码 C6F3U6FDgQLBFRRbAAo0o0o
        $cache = new Cache();
        $token = $request->input('document_access_token');
        if(!$token){
            return App::getApp()->getContext()->getResponse()->json(['message'=>'缺少用户票据','data'=>null,'status'=>false,'code'=>444]);
        }
        $access_token = $cache->get($token);
        if(!$access_token){
            return App::getApp()->getContext()->getResponse()->json(['message'=>'错误的票据','data'=>null,'status'=>false,'code'=>444]);
        }
        $request->document_user_id = $access_token;
        $logic = new App\Model\Logic\UserAuthorizationLogic();
        $request->document_user_auth = $logic->getUserAuthorizations($access_token);
        return $handler->handle($request);
    }
}
