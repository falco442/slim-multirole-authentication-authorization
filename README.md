# slim-multirole-authentication-authorization

## Introduction

This Middleware is made for [Slim Skeleton Application](https://github.com/slimphp/Slim-Skeleton). It uses Eloquent for its functionalities, so you are supposed that you follow the Slim 3's guide chapter [Using Eloquent with Slim](https://www.slimframework.com/docs/v3/cookbook/database-eloquent.html) to use Slim with Eloquent, the *Laravel* ORM.

This middleware verifies the presence of the `Authorization` header as it's determined by the W3 convention, and blocks/allows the other requests.

## Installation

Get it with composer

```bash
composer require falco442/slim-multirole-authentication-authorization
```

## Use

You can then bootstrap your app with your settings:

```PHP
require 'vendor/autoload.php';

use falco442\Middleware\Authentication\BasicHttpAuthentication;

// Create and configure Slim app
$config = [
    'settings' => [
        'addContentLengthHeader' => false,
        'displayErrorDetails' => true,
        // other settings
        'authentication' => [
            'userModel' => 'your-eloquent-model-with-namespace', // for example 'App\\Model\\User'
            'fields' => [
                'username' => 'email',
                'password' => 'password'
            ],
            'jsonResponse' => true, // if you want response in json format
            'unauthorizedMessage' => 'Your unauthorized message',
            'unauthorizedStatus' => 403 // Usually 403
        ]
    ]
];
$app = new \Slim\App($config);

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($config['settings']['db']);

$capsule->setAsGlobal();
$capsule->bootEloquent();


$app->get('/',function($request, $response, $args){
    return $response->withJson([1,2,3]);
})->add((new BasicHttpAuthentication($app->getContainer())));

// Run app
$app->run();
```
