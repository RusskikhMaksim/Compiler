<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'src/LexicalAnalyzeClasses/TokenFormation.php';
require_once 'src/LexicalAnalyzeClasses/CompleteToken.php';

class TokenFormationTest extends TestCase
{
    private object $tokenToFormat;
    private object $completeToken;

    protected function setUp(): void
    {
        $this->tokenToFormat = new TokenFormation();
        $this->completeToken = new CompleteToken();
    }

    protected function tearDown(): void
    {
        unset($this->tokenToFormat);
        unset($this->completeToken);
    }

    /**
     * @dataProvider additionProviderToLetterToken
     * @param string $symbol
     * @param int $arrayIndexOfCurrentElement
     * @param array $programCopy
     * @param int $stringCounter
     */
    public function testLetterToken(string $symbol, int $arrayIndexOfCurrentElement, array $programCopy, int $stringCounter, int $positionInStrIndex) {
        //$this->assertIsString($symbol);
        //$this->assertIsInt($arrayIndexOfCurrentElement);
        //$this->assertIsString($programCopy[$arrayIndexOfCurrentElement+1]);
        //$this->assertIsInt($stringCounter);

        $resultOfTest = $this->tokenToFormat->letterToken($symbol, $arrayIndexOfCurrentElement, $programCopy, $stringCounter, $positionInStrIndex);

        $this->assertIsObject($resultOfTest);
        $this->assertSame("int",$resultOfTest->bodyOfToken);
        $this->assertSame("datatype",$resultOfTest->tokenClass);
        $this->assertSame(0,$resultOfTest->startPositionInString);
        $this->assertSame(2,$resultOfTest->endPositionInString);
        $this->assertSame(0,$resultOfTest->NumOfStringInProgram);

    }


    /**
     * @dataProvider additionProviderToDigitToken
     * @param string $symbol
     * @param int $arrayIndexOfCurrentElement
     * @param array $programCopy
     * @param int $stringCounter
     */

    public function testDigitToken(string $symbol, int $arrayIndexOfCurrentElement, array $programCopy, int $stringCounter, int $positionInStrIndex){
        $resultOfTest = $this->tokenToFormat->digitToken($symbol, $arrayIndexOfCurrentElement, $programCopy, $stringCounter, $positionInStrIndex);

        $this->assertIsObject($resultOfTest);
        $this->assertSame("356",$resultOfTest->bodyOfToken);
        $this->assertSame("numeric_constant",$resultOfTest->tokenClass);
        $this->assertSame(0,$resultOfTest->startPositionInString);
        $this->assertSame(2,$resultOfTest->endPositionInString);
        $this->assertSame(0,$resultOfTest->NumOfStringInProgram);
    }


    /**
     * @dataProvider additionProviderToAllOther
     * @param string $symbol
     * @param int $arrayIndexOfCurrentElement
     * @param array $programCopy
     * @param int $stringCounter
     */

    public function testAllOtherTokens(string $symbol, int $arrayIndexOfCurrentElement, array $programCopy, int $stringCounter, int $positionInStrIndex){

        $resultOfTest = $this->tokenToFormat->allOtherTokens($symbol, $arrayIndexOfCurrentElement, $programCopy, $stringCounter, $positionInStrIndex);

        $this->assertIsObject($resultOfTest);
        $this->assertSame("<",$resultOfTest->bodyOfToken);
        $this->assertSame("less than",$resultOfTest->tokenClass);
        $this->assertSame(16,$resultOfTest->startPositionInString);
        $this->assertSame(16,$resultOfTest->endPositionInString);
        $this->assertSame(0,$resultOfTest->NumOfStringInProgram);

    }

    /**
     * @dataProvider addProvToAllOtherWithString
     * @param string $symbol
     * @param int $arrayIndexOfCurrentElement
     * @param array $programCopy
     * @param int $stringCounter
     */
    public function testAllOtherTokensStringLiteral(string $symbol, int $arrayIndexOfCurrentElement, array $programCopy, int $stringCounter, int $positionInStrIndex){

        $resultOfTest = $this->tokenToFormat->allOtherTokens($symbol, $arrayIndexOfCurrentElement, $programCopy, $stringCounter, $positionInStrIndex);

        $this->assertIsObject($resultOfTest);
        $this->assertSame("\"Hello, world!\"",$resultOfTest->bodyOfToken);
        $this->assertSame("string litheral",$resultOfTest->tokenClass);
        $this->assertEquals(11,$resultOfTest->startPositionInString);
        $this->assertEquals(25,$resultOfTest->endPositionInString);
        $this->assertEquals(3,$resultOfTest->NumOfStringInProgram);

    }

    public function addProvToAllOtherWithString(){
        $programCopyInStringForm = "    printf(\"Hello, world!\");\n";
        $programCopy = str_split("    printf(\"Hello, world!\");\n");
        $symbol ='"';
        $arrayIndexOfCurrentElement = stripos($programCopyInStringForm, '"');
        $stringCounter = 3;
        $positionInStrIndex = 11;
        return [
            ['"', $arrayIndexOfCurrentElement, $programCopy, $stringCounter, $positionInStrIndex]
        ];
    }


    public function additionProviderToLetterToken(){
        $programCopy = array();
        $programCopy[] = "i";
        $programCopy[] = "n";
        $programCopy[] = "t";
        $programCopy[] = " ";
        $index = 0;
        $positionInStrIndex = 0;
        return[
            ["i", $index, $programCopy, 0, $positionInStrIndex]
        ];

    }

    public function additionProviderToDigitToken(){
        $programCopy = array();
        $programCopy[] = "3";
        $programCopy[] = "5";
        $programCopy[] = "6";
        $programCopy[] = " ";
        $positionInStrIndex = 0;
        $index = 0;
        return[
            ["3", $index, $programCopy, 0, $positionInStrIndex]
        ];

    }

    public function additionProviderToAllOther(){
        $programCopy = array();
        $programCopy[] = "<";
        $programCopy[] = "\n";
        $positionInStrIndex = 16;
        $index = 16;
        return[
            ["<", $index, $programCopy, 0, $positionInStrIndex]
        ];
    }


}
