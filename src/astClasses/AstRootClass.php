<?php


class AstRootClass extends AstNode
{
    public string $typeOfNode = "";
    public object $parentNode;
    public object $childNode;
    public object $nextNode;
    public int $nestingLevel;

    public function __construct($currentNestingLevel) {
        $this->nestingLevel = $currentNestingLevel;

    }

    public function printNode(){
        $printLVL ="";
        for($i = 0; $i < $this->nestingLevel; $i++){
            $printLVL .= "-";
        }
        $printLVL .= ">";

        $printAssigmentNode = $printLVL . "[ Type: The root of the abstract syntax tree, value: " . "'" . "program" . "' ]\n";
        print($printAssigmentNode);
    }



}