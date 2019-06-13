<?php

namespace InsuranceInteractionTest\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use InsuranceInteraction\Middleware\GetPolicyViaAttributeMiddleware;
use InsuranceInteraction\Helper\ActivePolicyHelper;
use PHPUnit\Framework\TestCase;
use Mockery;


class GetPolicyViaAttributeMiddlewareTest extends TestCase
{

    public function setUp()
    {
        $this->mockRequest = Mockery::mock(ServerRequestInterface::class);
        $this->mockHandler = Mockery::mock(RequestHandlerInterface::class);

        $activePolicyHelper = Mockery::mock(ActivePolicyHelper::class);
        $this->getPolicyViaAttributeMiddleware = new GetPolicyViaAttributeMiddleware(
            $activePolicyHelper
        );

    }
    public function testItWorks()
    {
        $this->assertEquals(1, 1);
    }

    /**
     * @dataProvider responseAndIdentifierProvider
     */
    public function testItBringsBackTheCorrectResponseDependingOnPolicyOrIdentifier(
        $identifier,
        $isRequired,
        $expectedResult
    ) {
        $this->mockRequest
            ->shouldReceive('getAttribute')
            ->andReturn($identifier);

        $result = $this->getPolicyViaAttributeMiddleware->process(
            $this->mockRequest,
            $this->mockHandler
        );
    }

    public function responseAndIdentifierProvider()
    {
        return [
            "No identifier but is required" => [
                false,
                null
            ]
        ];
    }
}
