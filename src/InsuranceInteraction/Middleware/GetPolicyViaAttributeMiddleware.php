<?php

namespace InsuranceInteraction\Middleware;

use InsuranceInteraction\Helper\ActivePolicyHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class GetPolicyViaAttributeMiddleware implements MiddlewareInterface
{
    const CONFIG_IDENTIFIER_KEY        = 'identifier_key';
    const CONFIG_REQUIRED              = 'is_required';
    const DEFAULT_IDENTIFIER_KEY       = 'policyIdentifier';
    const DEFAULT_REQUIRED             = true;
    const MESSAGE_IDENTIFIER_NOT_FOUND = 'A policy identifier must be provided';
    const MESSAGE_QUOTATION_NOT_FOUND  = 'The requested policy could not be found';

    /** @var ActivePolicyHelper */
    private $helper;
    /** @var array */
    private $config;

    public function __construct(ActivePolicyHelper $helper, array $config = [])
    {
        $this->helper = $helper;
        $this->config = $this->resolveConfig($config);
    }

    protected function resolveConfig(array $config): array
    {
        $classConfig  = $config[static::class] ?? $config;
        $default      = [
            self::CONFIG_IDENTIFIER_KEY => self::DEFAULT_IDENTIFIER_KEY,
            self::CONFIG_REQUIRED       => self::DEFAULT_REQUIRED,
        ];
        $mergedConfig = array_merge($default, $classConfig);
        return array_intersect_key($mergedConfig, $default);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $identifier = $request->getAttribute($this->config[self::CONFIG_IDENTIFIER_KEY]);
        $found      = false;
        $required   = $this->config[self::CONFIG_REQUIRED];

        if ($required && !$identifier) {
            return $this->identifierNotFoundResult();
        }

        if ($identifier) {
            $found = $this->helper->loadPolicy($identifier);
        }

        if ($required && !$found) {
            return $this->policyNotFoundResult($identifier);
        }

        return $handler->handle($request);
    }

    private function identifierNotFoundResult()
    {
        return new JsonResponse(
            [
                'status' => 'fail',
                'data'   => [
                    $this->config[self::CONFIG_IDENTIFIER_KEY] => [
                        'value'    => null,
                        'messages' => [
                            self::MESSAGE_IDENTIFIER_NOT_FOUND
                        ]
                    ],
                ],
            ],
            400
        );
    }

    private function policyNotFoundResult(string $identifier)
    {
        return new JsonResponse(
            [
                'status' => 'fail',
                'data'   => [
                    $this->config[self::CONFIG_IDENTIFIER_KEY] => [
                        'value'    => $identifier,
                        'messages' => [
                            self::MESSAGE_QUOTATION_NOT_FOUND
                        ]
                    ],
                ],
            ],
            404
        );
    }

    public function getAttributeName()
    {
        return $this->config[self::CONFIG_IDENTIFIER_KEY];
    }
}
