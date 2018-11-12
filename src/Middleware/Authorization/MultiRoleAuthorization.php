<?php

namespace falco442\Middleware\Authorization;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class MultiRoleAuthorization{
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
        $response = $next($request, $response);

        return $response;
    }
}