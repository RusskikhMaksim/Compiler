<?php
require_once 'include.php';
require_once 'FunctionsOfParserToTest/printfParseFunc.php';

use PHPUnit\Framework\TestCase;

class printfParseTest extends TestCase
{
    public array $Token;
    public $handler;
    public $Lexer;
    public array $tokenArr;
    public int $tokenArrayIndex;
    public object $currentNonterminal;
    public object $currentParent;
    public object $currentToken;
    public int $nestingLevelCounter = 0;

    //public object $previousNonterminal;

    public function setUp(): void
    {
        $this->handler = fopen("tests/printfTestData.c", "r");
        $this->tokenArr = array();
        $this->Lexer = 'myLexer';
        $this->Token = myLexer($this->handler, $this->tokenArr);
        $this->tokenArrayIndex = 0;
        $this->currentNonterminal =  new AstDeclarationClass($this->nestingLevelCounter);
        $this->currentNonterminal->typeOfNode = "Function declaration";
        $this->currentParent = $this->currentNonterminal;


        $this->currentNonterminal->parentNode = new AstRootClass($this->nestingLevelCounter);
        $this->currentNonterminal->parentNode->typeOfNode = "Program";
        $this->currentNonterminal->parentNode->childNode = $this->currentNonterminal;
        $this->currentToken = new CompleteToken();


    }

    public function tearDown(): void
    {
        unset($this->currentToken);
        unset($this->currentNonterminal);
        unset($this->currentParent);

    }

    public function testFunctionCallOrVariableAssignment(){
        $this->currentToken = NextToken($this);
        $result = inputOrOutputNode($this->currentNonterminal, $this->currentParent, $this->currentToken, $this, $this->nestingLevelCounter);

        $this->assertIsObject($result);
        $this->assertSame("Calling a library function", $result->typeOfNode);
        $this->assertIsObject($result->parentNode);
        $this->assertSame("Function declaration", $result->parentNode->typeOfNode);
        $this->assertIsObject($result->parentNode->parentNode);
        $this->assertSame("Program", $result->parentNode->parentNode->typeOfNode);
    }





}

