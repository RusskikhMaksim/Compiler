<?php
declare(strict_types=1);

class AstClass
{
    public object $rootOfAST;
    public string $typeOfNode = "";
    public int $nestingLevel;

    public function printAST() : void{
        echo 1;
    }

    public function __construct($currentNestingLevel) {
        $this->nestingLevel = $currentNestingLevel;
        $this->typeOfNode = "Abstract Syntax Tree";
    }

}

