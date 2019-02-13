<?php
/*
 * This file is part of Swagger Mock.
 *
 * (c) Igor Lazarev <strider2038@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Unit\OpenAPI\Parsing;

use App\Mock\Parameters\MockEndpoint;
use App\Mock\Parameters\MockResponseCollection;
use App\OpenAPI\Parsing\EndpointParser;
use App\OpenAPI\Parsing\SpecificationAccessor;
use App\OpenAPI\Parsing\SpecificationPointer;
use App\Tests\Utility\TestCase\ParsingTestCaseTrait;
use PHPUnit\Framework\TestCase;

class EndpointParserTest extends TestCase
{
    use ParsingTestCaseTrait;

    private const RESPONSE_SPECIFICATION = ['response_specification'];
    private const RESPONSE_STATUS_CODE = '200';
    private const VALID_ENDPOINT_SCHEMA = [
        'responses' => [
            self::RESPONSE_STATUS_CODE => self::RESPONSE_SPECIFICATION,
        ],
    ];

    protected function setUp(): void
    {
        $this->setUpParsingContext();
    }

    /** @test */
    public function parsePointedSchema_validResponseSpecification_mockEndpointWithResponses(): void
    {
        $parser = $this->createEndpointParser();
        $expectedMockResponses = new MockResponseCollection();
        $this->givenContextualParser_parsePointedSchema_returns($expectedMockResponses);
        $specification = new SpecificationAccessor(self::VALID_ENDPOINT_SCHEMA);

        /** @var MockEndpoint $endpoint */
        $endpoint = $parser->parsePointedSchema($specification, new SpecificationPointer());

        $this->assertContextualParser_parsePointedSchema_wasCalledOnceWithSpecificationAndPointerPath(
            $specification,
            ['responses']
        );
        $this->assertSame($expectedMockResponses, $endpoint->responses);
    }

    private function createEndpointParser(): EndpointParser
    {
        return new EndpointParser($this->contextualParser);
    }
}
