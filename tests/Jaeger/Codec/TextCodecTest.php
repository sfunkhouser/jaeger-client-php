<?php

namespace Jaeger\Codec;

use Jaeger\SpanContext;
use PHPUnit\Framework\TestCase;

class TextCodecTest extends TestCase
{
    /** @var TextCodec */
    private $textCodec;

    /** @var SpanContext */
    private $ctx;

    public function setUp()
    {
        $this->ctx = new SpanContext('trace-id', 'span-id', null, null);
        $this->textCodec = new TextCodec();
    }

    public function testCanInjectContextInCarrier()
    {
        $carrier = [];

        $this->textCodec->inject($this->ctx, $carrier);

        $this->assertFalse(empty($carrier));
    }

    public function testSpanContextParsingFromHeader()
    {
        $carrier = ['uber-trace-id' => '32834e4115071776:f7802330248418d:32834e4115071776:1'];

        $spanContext = $this->textCodec->extract($carrier);

        self::assertEquals("1717370599544170", $spanContext->getTraceId());
        self::assertEquals("641546560935337", $spanContext->getSpanId());
        self::assertEquals("1717370599544170", $spanContext->getParentId());
        self::assertEquals(1, $spanContext->getFlags());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Malformed tracer state string
     */
    public function testInvalidSpanContextParsingFromHeader()
    {
        $carrier = ['uber-trace-id' => 'invalid_data'];

        $this->textCodec->extract($carrier);
    }
}
