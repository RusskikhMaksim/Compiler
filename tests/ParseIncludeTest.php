<?php
require_once 'include.php';
require_once 'FunctionsOfParserToTest/inclDir.php';

use PHPUnit\Framework\TestCase;

class ParseIncludeTest extends TestCase
{
    public array $Token;
    public $handler;
    public $Lexer;
    public array $tokenArr;
    public int $tokenArrayIndex;
    public object $currentNonterminal;
    public object $currentParent;
    public object $currentToken;
    public int $nestingLevel;

        //тест под иклюд
    public function setUp(): void
    {
        $this->handler = fopen("tests/includeTestData.c", "r");
        $this->nestingLevel = 0;
        $this->tokenArr = array();
        $this->Lexer = 'myLexer';
        $this->Token = myLexer($this->handler, $this->tokenArr);
        $this->tokenArrayIndex = 0;
        $this->currentNonterminal = new AstRootClass($this->nestingLevel);
        $this->currentNonterminal->setTypeOfNode("Program");
        $this->currentParent = $this->currentNonterminal;
        $this->currentToken = new CompleteToken();
    }

    public function tearDown(): void
    {
        unset($this->currentToken);
        unset($this->currentNonterminal);
        unset($this->currentParent);
    }


    public function testPreprocessorDirectiveNodeFunc(){
        $this->currentToken = NextToken($this);
        $result = preprocessorDirectiveNodeFunc($this->currentParent, $this->currentToken, $this, $this->nestingLevel);

        $this->assertIsObject($result);
        $this->assertSame("Preprocessor directive include", $result->typeOfNode);
        $this->assertIsObject($result->parentNode);
        $this->assertSame("Program", $result->parentNode->typeOfNode);
        $this->assertIsString($result->directive);
        $this->assertSame("#include", $result->directive);
        $this->assertIsString($result->calleeLib);
        $this->assertSame("<stdio.h>", $result->calleeLib);
    }



    //тест под инт мейн
    //тест под принтф
    //тест под ретёрн
    //


}
