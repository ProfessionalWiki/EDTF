<?php

declare(strict_types=1);

namespace EDTF\Tests\Unit;


use EDTF\ExtDate;
use EDTF\Interval;
use EDTF\PackagePrivate\Parser;
use PHPUnit\Framework\TestCase;

/**
 * Class ParserTest
 *
 * @covers \EDTF\PackagePrivate\Parser
 * @package EDTF\Tests\Unit
 */
class ParserTest extends TestCase
{
    private function createParser($data): Parser
    {
        $parser = new Parser();
        $parser->parse($data);
        return $parser;
    }

    public function testCreatingEdtfObjects()
    {
        $parser = new Parser();
        $this->assertInstanceOf(ExtDate::class, $parser->createEdtf('2016-03-01'));
        $this->assertInstanceOf(Interval::class, $parser->createEdtf('2016/2019'));
    }

    public function testThrowExceptionWhenCreatingEdtfFromEmptyString()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->createParser("");
    }

    public function testThrowExceptionOnInvalidDataFormat()
    {
        $this->expectException(\InvalidArgumentException::class);

        $parser = new Parser();
        $parser->createEdtf('foo');
    }

    public function testShouldStoreInput()
    {
        $parser = $this->createParser('1984');
        $this->assertSame('1984', $parser->getInput());
    }

    public function testShouldParseCompleteDate()
    {
        $parser = new Parser();
        $parser->createEdtf('2004-01-02');

        $this->assertSame(2004, $parser->getYearNum());
        $this->assertSame(1, $parser->getMonthNum());
        $this->assertSame(2, $parser->getDayNum());
    }

    public function testShouldParseCompleteDateTime()
    {
        $parser = new Parser();
        $parser->createEdtf('2004-01-02T23:59:59Z');

        $this->assertSame(2004, $parser->getYearNum());
        $this->assertSame(1, $parser->getMonthNum());
        $this->assertSame(2, $parser->getDayNum());
        $this->assertSame(23, $parser->getHour());
        $this->assertSame(59, $parser->getMinute());
        $this->assertSame(59, $parser->getSecond());
    }

    public function testShouldParseUTCTimezone()
    {
        $parser = new Parser();
        $parser->createEdtf('2004-01-01T10:10:10Z');

        $this->assertSame(2004, $parser->getYearNum());
        $this->assertSame(1, $parser->getMonthNum());
        $this->assertSame(1, $parser->getDayNum());
        $this->assertSame(10, $parser->getHour());
        $this->assertSame(10, $parser->getMinute());
        $this->assertSame(10, $parser->getSecond());
        $this->assertSame("Z", $parser->getTzUtc());
    }

    public function testShouldParseTimezoneValue()
    {
        $parser = new Parser();
        $parser->createEdtf('2004-01-01T10:10:10+05:30');

        $this->assertSame(2004, $parser->getYearNum());
        $this->assertSame(1, $parser->getMonthNum());
        $this->assertSame(1, $parser->getDayNum());
        $this->assertSame(10, $parser->getHour());
        $this->assertSame(10, $parser->getMinute());
        $this->assertSame(10, $parser->getSecond());
        $this->assertSame(5, $parser->getTzHour());
        $this->assertSame(30, $parser->getTzMinute());
    }

    public function testShouldParseLetterPrefixedCalendarYear()
    {
        $parser = $this->createParser('Y170000002');
        $this->assertSame(170000002, $parser->getYearNum());

        $parser = $this->createParser('Y-170000002');
        $this->assertSame(-170000002, $parser->getYearNum());
    }

    public function testShouldParseSeason()
    {
        $parser = $this->createParser('2001-21');
        $this->assertNull($parser->getMonthNum());
        $this->assertSame(21, $parser->getSeason());
    }

    public function testThrowExceptionOnInvalidSeasonNumber()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->createParser('2001-99');
    }

    public function testShouldParseQualificationWithinYear()
    {
        $parser = $this->createParser('?1984');
        $this->assertSame(1984, $parser->getYearNum());
        $this->assertSame("?", $parser->getYearOpenFlag());

        $parser = $this->createParser('1984?');
        $this->assertSame(1984, $parser->getYearNum());
        $this->assertSame("?", $parser->getYearCloseFlag());
    }

    public function testShouldParseQualificationWithinMonth()
    {
        $parser = $this->createParser("1984-02%");
        $this->assertSame(2, $parser->getMonthNum());
        $this->assertSame("%", $parser->getMonthCloseFlag());

        $parser = $this->createParser("1984-02~");
        $this->assertSame(2, $parser->getMonthNum());
        $this->assertSame("~", $parser->getMonthCloseFlag());
    }

    public function testShouldParseQualificationWithinDay()
    {
        $parser = $this->createParser("1984-02-01~");
        $this->assertSame(2, $parser->getMonthNum());
        $this->assertSame(1, $parser->getDayNum());
        $this->assertSame("~", $parser->getDayCloseFlag());

        $parser = $this->createParser("1984-02-01%");
        $this->assertSame(2, $parser->getMonthNum());
        $this->assertSame(1, $parser->getDayNum());
        $this->assertSame("%", $parser->getDayCloseFlag());
    }

    public function testShouldParseQualificationInDatePart()
    {
        $parser = $this->createParser('~1984-%02-?01');
        $this->assertSame("~", $parser->getYearOpenFlag());
        $this->assertSame("%", $parser->getMonthOpenFlag());
        $this->assertSame("?", $parser->getDayOpenFlag());
    }

    public function testParseUnspecifiedDigitWithYearPrecision()
    {
        $parser = $this->createParser('201X');
        $this->assertSame(2010, $parser->getYearNum());
        $this->assertSame("201X", $parser->getYear());

        $parser = $this->createParser('20XX');
        $this->assertSame(2000, $parser->getYearNum());
        $this->assertSame("20XX", $parser->getYear());
    }

    public function testParseUnspecifiedDigitWithMonthPrecision()
    {
        $parser = $this->createParser('2010-XX');
        $this->assertSame(2010, $parser->getYearNum());
        $this->assertNull($parser->getMonthNum());
    }

    public function testParseUnspecifiedDigitWithDayPrecision()
    {
        $parser = $this->createParser('2010-12-XX');
        $this->assertSame(2010, $parser->getYearNum());
        $this->assertSame(12, $parser->getMonthNum());
        $this->assertNull($parser->getDayNum());
    }

    public function testParseUnspecifiedDigitWithMixedPrecision()
    {
        $parser = $this->createParser("20XX-XX-XX");
        $this->assertSame(2000, $parser->getYearNum());
        $this->assertNull($parser->getMonthNum());
        $this->assertNull($parser->getDayNum());
    }
}