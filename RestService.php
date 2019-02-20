<?php

namespace go1\rest;

use DI\ContainerBuilder;
use Firebase\JWT\JWT;
use Slim\Http\Request;
use Slim\Http\Response;

class RestService extends \DI\Bridge\Slim\App
{
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;

        parent::__construct();

        # Middleware to convert JWT from query|header|cookie -> attribute jwt.payload
        # Note: This is not JWT validation
        $this->add(
            function (Request $request, Response $response, callable $next) {
                $auth = $request->getHeader('Authorization');
                if ($auth && (0 === strpos('Bearer ', $auth))) {
                    $jwt = substr($auth, 7);
                }

                $jwt = $jwt ?? $request->getQueryParam('jwt') ?? $request->getCookieParam('jwt');
                $jwt = is_null($jwt) ? null : (2 !== substr_count($jwt, '.')) ? null : explode('.', $jwt)[1];
                $jwt = is_null($jwt) ? null : JWT::jsonDecode(JWT::urlsafeB64Decode($jwt));

                return $next(
                    is_null($jwt) ? $request : $request->withAttribute('jwt.payload', $jwt),
                    $response
                );
            }
        );
    }

    protected function configureContainer(ContainerBuilder $builder)
    {
        if (!empty($this->config)) {
            $this->config = [];
            $builder->addDefinitions($this->config);
        }
    }
}