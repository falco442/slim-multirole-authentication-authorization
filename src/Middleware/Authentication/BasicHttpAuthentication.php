<?php

namespace falco442\Middleware\Authentication;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class BasicHttpAuthentication{
    protected $_config = [
        'userModel' => 'falco442\\Model\\User',
        'fields' => [
            'username' => 'username',
            'password' => 'password'
        ],
        'jsonResponse' => true,
        'unauthorizedMessage' => "Unauthorized",
        'unauthorizedStatus' => 403
    ];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next){
        $httpHost = $this->container['environment']['HTTP_HOST'];
        $authHeader = $request->getHeader('Authorization');
        if(!$authHeader || !count($authHeader)){
            return $response->withStatus(401)->withHeader('WWW-Authenticate', sprintf('Basic realm="%s"',$httpHost));
        }
        $base64 = str_replace('Basic ', '', $authHeader[0]);
        $encoded = base64_decode($base64);

        list($user, $password) = explode(':', $encoded);

        $password = password_hash($password, PASSWORD_DEFAULT);

        $config = $this->container->get('settings');

        if(!isset($config['authentication'])){
            $config['authentication'] = [];
        }

        $authConfig = array_replace_recursive($this->_config, $config['authentication']);

        $model = $authConfig['userModel'];

        $user = $model::where($authConfig['fields']['username'], '=', $username)
            ->where($authConfig['fields']['password'], '=', $password)
            ->first();

        if(!$user){
            $response = $response->withStatus($authConfig['unauthorizedStatus']);
            if($authConfig['jsonResponse']){
                $response = $response->withJson($authConfig['unauthorizedMessage']);
            }else{
                $response = $response->write($authConfig['unauthorizedMessage']);
            }
            return $response;
        }

        $response = $next($request, $response);

        return $response;
    }
}