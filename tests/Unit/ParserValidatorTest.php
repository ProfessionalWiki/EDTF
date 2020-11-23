<?php

namespace EDTF\Tests\Unit;

use EDTF\Parser;
use EDTF\ParserValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ParserValidatorTest
 *
 * @covers \EDTF\ParserValidator
 * @package EDTF\Tests\Unit
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

    public function testValidateInput()
    {
        $parser = $this->parser;
        $validator = $this->validator;

        $parser->expects($this->once())
            ->method('getMatches')
            ->willReturn(['yearNum' => "", 'monthNum' => ""]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/input/');
        $validator->validateInput();
    }

    public function testValidateSeason()
    {
        $parser = $this->parser;
        $validator = $this->validator;

        $parser->expects($this->once())
            ->method('getSeason')
            ->willReturn(19);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid season number "19" in "input" is out of range. Accepted season number is between 21-41');
        $validator->validateSeason();
    }
}
