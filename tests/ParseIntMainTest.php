<?php
require_once 'include.php';
require_once 'FunctionsOfParserToTest/intMain.php';

use PHPUnit\Framework\TestCase;

class ParseIntMainTest extends TestCase
{
    public array $Token;
    public $handler;
    public $Lexer;
    public array $tokenArr;
    public int $tokenArrayIndex;
    public object $currentNonterminal;
    public object $currentParent;
    public object $ast;
    public object $currentToken;
    public int $nestingLevelCounter = 0;

    //public object $previousNonterminal;

    public function setUp(): void
    {
        $this->handler = fopen("tests/intMainTestData.c", "r");
        $this->tokenArr = array();
        $this->Lexer = 'myLexer';
        $this->Token = myLexer($this->handler, $this->tokenArr);
        $this->tokenArrayIndex = 0;
        $this->currentNonterminal = new AstPreprocessorDirectiveClass($this->nestingLevelCounter);
        $this->currentNonterminal->typeOfNode = "Preprocessor directive";
        $this->currentParent = new AstRootClass($this->nestingLevelCounter);
        $this->ast = new AstClass($this->nestingLevelCounter);
        $this->ast->rootOfAST = $this->currentParent;
        $this->currentParent->parentNode = $this->ast;
        $this->currentParent->typeOfNode = "Program";
        //$this->currentParent->childNode = $this->currentNonterminal;
        //$this->currentNonterminal->parentNode = $this->currentParent;
        $this->currentToken = new CompleteToken();


    }

    public function tearDown(): void
    {
        unset($this->currentToken);
        unset($this->currentNonterminal);
        unset($this->currentParent);
        unset($this->previNonterminal);
    }

    public function testDeclareFuncOrIdFunc()
    {
        $this->currentToken = NextToken($this);
        $result = DeclareSomething($this->currentNonterminal, $this->currentParent, $this->currentToken, $this, $this->nestingLevelCounter);

        $this->assertIsObject($result);
        $this->assertSame("Function declaration", $result->typeOfNode);
        $this->assertIsObject($result->parentNode);
        $this->assertSame("Program", $result->parentNode->typeOfNode);
        $this->assertIsObject($result->dataTypeAndId);
        $this->assertSame("Data type and function name", $result->dataTypeAndId->typeOfNode);
    }


}
