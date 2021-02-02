<?php

namespace EDTF\Tests\Unit\PackagePrivate;

use EDTF\PackagePrivate\Parser;
use EDTF\PackagePrivate\ParserValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\ParserValidator
 * @package EDTF\Tests\Unit\PackagePrivate
 */
class ParserValidatorTest extends TestCase
{
    /**
     * @var ParserValidator
     */
    private $validator;

    /**
     * @var MockObject|Parser
     */
    private $parser;

    public function setUp(): void
    {
        $this->parser = $this->createMock(Parser::class);
        $this->parser->expects($this->any())
            ->method('getInput')
            ->willReturn('input');
        $this->validator = new ParserValidator($this->parser);
    }

    public function testSuccessValidation()
    {
        $this->parser->expects($this->once())
            ->method('getMatches')
            ->willReturn(['yearNum' => '1987', 'monthNum' => '12']);

        $this->parser->expects($this->once())
            ->method('getSeason')
            ->willReturn(24);

        $this->validator->isValid();

        $this->assertEquals("", $this->validator->getMessages());
    }

    /**
     * @dataProvider invalidDataTypeProvider
     * @param $yearNum
     * @param $monthNum
     * @param array $wrongKeyNames
     */
    public function testDataTypeFailsInputValidation($yearNum, $monthNum, $wrongKeyNames = [])
    {
        $this->parser->expects($this->once())
            ->method('getMatches')
            ->willReturn(['yearNum' => $yearNum, 'monthNum' => $monthNum]);

        $this->validator->isValid();
        foreach ($wrongKeyNames as $keyName) {
            $this->assertStringContainsString("Invalid data format: $keyName must be a string", $this->validator->getMessages());
        }
    }

    public function testEmptyStringsFailInputValidation()
    {
        $this->parser->expects($this->once())
            ->method('getMatches')
            ->willReturn(['yearNum' => '', 'monthNum' => '']);

        $this->validator->isValid();
        $this->assertEquals("Invalid edtf format input", $this->validator->getMessages());
    }

    public function testInvalidSeasonValueFailsSeasonValidation()
    {
        $this->parser->expects($this->once())
            ->method('getMatches')
            ->willReturn(['yearNum' => '1987', 'monthNum' => '10']);

        $this->parser->expects($this->once())
            ->method('getSeason')
            ->willReturn(19);

        $this->validator->isValid();
        $this->assertEquals(
            "Invalid season number 19 in input is out of range. Accepted season number is between 21-41",
            $this->validator->getMessages()
        );
    }

    public function invalidDataTypeProvider()
    {
        return [
            [1987, '10', ['yearNum']],
            ['1987', 10, ['monthNum']],
            [10.0, 12, ['yearNum', 'monthNum']],
            [null, '10', ['yearNum']],
            [false, null, ['yearNum', 'monthNum']]
        ];
    }
}
