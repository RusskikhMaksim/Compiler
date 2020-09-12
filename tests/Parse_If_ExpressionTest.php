<?php
require_once 'include.php';
require_once 'FunctionsOfParserToTest/If_ExprParseFunc.php';

use PHPUnit\Framework\TestCase;

class Parse_If_ExpressionTest extends TestCase
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
        $this->handler = fopen("tests/ifSTMTTestData.c", "r");
        $this->tokenArr = array();
        $this->Lexer = 'myLexer';
        $this->Token = myLexer($this->handler, $this->tokenArr);
        $this->tokenArrayIndex = 0;
        $this->currentNonterminal = new AstIfClass($this->nestingLevelCounter);
        $this->currentNonterminal->typeOfNode = "conditional jump operator if";
        $this->currentParent = new AstDeclarationClass($this->nestingLevelCounter);
        $this->currentParent->setTypeOfNode("Function declaration");
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

    public function testExpressionNodeFunc()
    {
        //foreach ($this->Token as $ti){
        //    print_r($ti);
        // }
        //$this->currentToken = NextToken($this);
        //var_dump($this->currentToken);
        $result = expressionNode($this->currentNonterminal, $this->currentToken, $this, $this->nestingLevelCounter);

        $this->assertIsObject($result);
        $this->assertSame("conditional jump operator if", $result->typeOfNode);
        $this->assertIsObject($result->parentNode);
        $this->assertSame("Function declaration", $result->parentNode->typeOfNode);
        $this->assertIsObject($result->ifSTMTCondition);
        $this->assertSame("conditional if expression", $result->ifSTMTCondition->typeOfExpression);
        $this->assertIsArray($result->ifSTMTCondition->partsOfExpression);
        //var_dump($result->ifSTMTCondition->partsOfExpression);
        $this->assertArrayHasKey("type of data", $result->ifSTMTCondition->partsOfExpression[0]);
    }
}
