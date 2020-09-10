<?php


class AstIfClass extends AstNode
{
    public string $typeOfNode = "";
    public string $bodyOfNode = "";
    public object $parentNode;
    public object $childNode;
    public object $nextNode;
    public int $nestingLevel;
    public object $ifSTMTCondition;


    public function __construct($currentNestingLevel) {
        $this->ifSTMTCondition = new AstExpression();
        $this->nestingLevel = $currentNestingLevel;
    }


    public function printNode(){
        $printLVL ="";
        for($i = 0; $i < $this->nestingLevel; $i++){
            $printLVL .= "-";
        }

        $printLVL .= ">";
        $printIfNode = $printLVL . "[ Type: " . $this->typeOfNode . ", value: " . "'" . $this->bodyOfNode . "' ]\n";
        print($printIfNode);


        if($this->bodyOfNode === "if") {
            $printIfSTMTCondition = $printLVL . "[ Type: Execution condition ]\n";
            print($printIfSTMTCondition);

            foreach ($this->ifSTMTCondition->partsOfExpression as $value) {

                $printData = $printLVL . "\t[ Type: " . $value["type of data"] . ", data: " . "'" . $value["data"] . "' ]\n";
                print($printData);
            }

        }
      $bodyOfConditionalOperator =  $printLVL . "[ Type: Body of conditional operator ]\n";
      print($bodyOfConditionalOperator);
    }
}



