<?php
require_once 'include.php';
require_once 'FunctionsOfParserToTest/returnParseFunc.php';

use PHPUnit\Framework\TestCase;

class ParseReturnFuncTest extends TestCase
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

    public function setUp(): void
    {
        $this->handler = fopen("tests/returnTestData.c", "r");
        $this->tokenArr = array();
        $this->Lexer = 'myLexer';
        $this->Token = myLexer($this->handler, $this->tokenArr);
        $this->tokenArrayIndex = 0;
        $this->currentNonterminal = new AstLibFuncClass($this->nestingLevelCounter);
        $this->currentNonterminal->setTypeOfNode("Calling a library function");
        $this->currentParent = new AstDeclarationClass($this->nestingLevelCounter);
        $this->currentParent->typeOfNode = "Function declaration";
        $this->currentParent->childNode = $this->currentNonterminal;
        $this->currentNonterminal->parentNode = $this->currentParent;
        $this->currentToken = new CompleteToken();
    }

    public function tearDown(): void
    {
        unset($this->currentToken);
        unset($this->currentNonterminal);
        unset($this->currentParent);
    }

    public function testKeyWordReturnFunc(){
        $this->currentToken = NextToken($this);
        $result = keyWordReturnNode($this->currentNonterminal, $this->currentParent, $this->currentToken, $this, $this->nestingLevelCounter);

        $this->assertIsObject($result);
        $this->assertSame("KeyWord return", $result->typeOfNode);
        $this->assertIsObject($result->parentNode);
        $this->assertIsObject($result->parentNode->childNode);
        $this->assertSame("Calling a library function", $result->parentNode->childNode->typeOfNode);
        $this->assertIsObject($result->parentNode->childNode->nextNode);
        $this->assertEquals($result, $result->parentNode->childNode->nextNode);


    }

}
