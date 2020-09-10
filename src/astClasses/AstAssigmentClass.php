<?php


class AstAssigmentClass extends AstNode
{
    public string $typeOfNode = "";
    public object $parentNode;
    public object $nextNode;
    public int $nestingLevel;

    public object $variableToAssigning;
    public object $dataToBeAssigned;

    public function __construct($currentNestingLevel)
    {
        $this->dataToBeAssigned = new AstExpression();
        $this->variableToAssigning = new IdClass();
        $this->nestingLevel = $currentNestingLevel;
    }

    public function printNode(){
        $printLVL ="";
        for($i = 0; $i < $this->nestingLevel; $i++){
            $printLVL .= "-";
        }
        $printLVL .= ">";
        $printAssigmentNode = $printLVL . "[ Type: " . $this->typeOfNode . ", value: " . "'" . "=" . "' ]\n";
        print($printAssigmentNode);

        $printVariable = $printLVL . "\t[ Type: variable to assigning, " . "'" . $this->variableToAssigning->id . "' ]\n";
        print($printVariable);

        $printDataToAssign = $printLVL . "\t[ Type: data to be assigning ]\n";
        print($printDataToAssign);

        foreach ($this->dataToBeAssigned->partsOfExpression as $value){

            $printData = $printLVL . "\t\t[ Type: " . $value["type of data"] . ", data: " . "'" .$value["data"] . "' ]\n";
            print($printData);
        }
    }
}