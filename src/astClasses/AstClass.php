<?php
declare(strict_types=1);

class AstClass
{
    public object $childNode;
    public string $typeOfNode = "";
    public int $nestingLevel;

    public function __construct($currentNestingLevel) {
        $this->nestingLevel = $currentNestingLevel;
        $this->typeOfNode = "Abstract Syntax Tree";
    }

    public function printNode(){

        $printAST = "[ Abstract syntax tree ]\n";
        print($printAST);
    }
}

