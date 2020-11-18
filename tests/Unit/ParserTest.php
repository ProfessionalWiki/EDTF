<?php

declare(strict_types=1);

namespace EDTF\Tests\Unit;


use EDTF\Parser;
use PHPUnit\Framework\TestCase;

/**
 * Class ParserTest
 *
 * @covers \EDTF\Parser
 * @package EDTF\Tests\Unit
 */
class ParserTest extends TestCase
{
    public function testShouldParseCompleteDate()
    {
        $parser = new Parser();
        $parser->parse('2004-01-02');

        $this->assertSame(2004, $parser->getYear());
        $this->assertSame(1, $parser->getMonth());
        $this->assertSame(2, $parser->getDay());
    }

    public function testShouldParseCompleteDateTime()
    {
        $parser = new Parser();
        $parser->parse('2004-01-02T23:59:59Z');

        $this->assertSame(2004, $parser->getYear());
        $this->assertSame(1, $parser->getMonth());
        $this->assertSame(2, $parser->getDay());
        $this->assertSame(23, $parser->getHour());
        $this->assertSame(59, $parser->getMinute());
        $this->assertSame(59, $parser->getSecond());
    }

    public function testShouldParseUTCTimezone()
    {
        $parser = new Parser();
        $parser->parse('2004-01-01T10:10:10Z');

        $this->assertSame(2004, $parser->getYear());
        $this->assertSame(1, $parser->getMonth());
        $this->assertSame(1, $parser->getDay());
        $this->assertSame(10, $parser->getHour());
        $this->assertSame(10, $parser->getMinute());
        $this->assertSame(10, $parser->getSecond());
        $this->assertSame("Z", $parser->getTzUtc());
    }

    public function testShouldParseTimezoneValue()
    {
        $parser = new Parser();
        $parser->parse('2004-01-01T10:10:10+05:30');

        $this->assertSame(2004, $parser->getYear());
        $this->assertSame(1, $parser->getMonth());
        $this->assertSame(1, $parser->getDay());
        $this->assertSame(10, $parser->getHour());
        $this->assertSame(10, $parser->getMinute());
        $this->assertSame(10, $parser->getSecond());
        $this->assertSame(5, $parser->getTzHour());
        $this->assertSame(30, $parser->getTzMinute());
    }

    public function testShouldParseEmptyString()
    {
        $parser = new Parser();
        $parser->parse('');
        $this->assertNull($parser->getYear());
    }

    public function testThrowExceptionOnInvalidDataFormat()
    {
        $this->expectException(\InvalidArgumentException::class);

        $parser = new Parser();
        $parser->parse('foo');
    }
}