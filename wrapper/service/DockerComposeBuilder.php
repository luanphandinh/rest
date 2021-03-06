<?php

namespace go1\rest\wrapper\service;

use go1\rest\wrapper\Manifest;

class DockerComposeBuilder
{
    private $builder;
    private $config = [];

    public function __construct(Manifest $builder)
    {
        $this->builder = $builder;
        $this->config = [
            'version'  => '2.0',
            'services' => [
                'web' => [
                    'environment' => [],
                ],
            ],
        ];
    }

    public function withEnv(string $name, ?string $value = null)
    {
        $this->config['services']['web']['environment'][] = is_null($value) ? "{$name}" : "{$name}={$value}";

        return $this;
    }

    public function end(): Manifest
    {
        return $this->builder;
    }

    public function build()
    {
        return $this->config;
    }
}
