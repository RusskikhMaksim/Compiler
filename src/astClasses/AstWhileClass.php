<?php
require_once 'include.php';
require_once 'AstExpression.php';

class AstWhileClass extends AstNode
{
    public string $typeOfNode = "";
    public object $loopCondition;
    public object $parentNode;
    //начало тела цикла
    public object $childNode;
    public object $nextNode;
    public int $NumOfStringInProgram = 0;
    public object $symbolTable;

    public function __construct($currentNestingLevel) {
        $this->loopCondition = new AstExpression();
        $this->nestingLevel = $currentNestingLevel;

    }

    public function printNode(){
        $printLVL ="";
        for($i = 0; $i <= $this->nestingLevel; $i++){
            $printLVL .= "-";
        }

        $printLVL .= ">";
        $printIfNode = $printLVL . "[ Type: " . $this->typeOfNode . ", value: " . "'" . "while" . "' ]\n";
        print($printIfNode);



            $printIfSTMTCondition = $printLVL . "[ Type: Execution condition ]\n";
            print($printIfSTMTCondition);

            foreach ($this->loopCondition->partsOfExpression as $value) {

                $printData = $printLVL . "\t[ Type: " . $value["type of data"] . ", data: " . "'" . $value["data"] . "' ]\n";
                print($printData);
            }


        $bodyOfWhileLoop =  $printLVL . "[ Type: Body of while loop ]\n";
        print($bodyOfWhileLoop);
    }





}