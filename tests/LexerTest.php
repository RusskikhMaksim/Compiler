<?php


use PHPUnit\Framework\TestCase;
require_once 'src/LexicalAnalyzeClasses/LexicalAnalyze.php';

class LexerTest extends TestCase
{
    private $lexicalAnalyzer;

    protected function setUp(): void
    {
        $this->lexicalAnalyzer = new LexicalAnalyze();
    }

    protected function tearDown(): void
    {
        $this->lexicalAnalyzer = NULL;
    }

    /**
     * @dataProvider additionProviderToLetterFunc
     */
    public function testLetter($testData, $expected){


        $resultOfTest = $this->lexicalAnalyzer->letter($testData);

        $this->assertEquals($expected, $resultOfTest);
    }

    /**
     * @dataProvider additionProviderToDigitFunc
     */
    public function testDigit($testData, $expected){

        $resultOfTest = $this->lexicalAnalyzer->digit($testData);

        $this->assertEquals($expected, $resultOfTest);
    }

    public function additionProviderToLetterFunc(){
        $testArr = array();
        $testArr[] = "i";
        $testArr[] = "n";
        $index = 0;
        return[
          'MUST_TO_BE_PASSED_letter_y' => ['y', true],
          'MUST_TO_BE_FAILED_digit_two' => ['2', false],
          'MUST_TO_BE_FAILED_another_symbol' => ['-', false],
          'testArr' => [$testArr[$index + 1], true]
        ];
    }

    public function additionProviderToDigitFunc(){
        return[
            'MUST_TO_BE_PASSED_digit_five' => ['5', true],
            'MUST_TO_BE_FAILED_letter_t' => ['t', false],
            'MUST_TO_BE_FAILED_another_symbol' => [':', false]
        ];
    }


}
