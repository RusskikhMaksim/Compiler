<?php


class AstIfClass extends AstNode
{
    public string $typeOfNode = "";
    public object $parentNode;
    public object $childNode;
    public object $nextNode;
    public int $nestingLevel;
    public object $ifSTMTCondition;


    public function __construct($currentNestingLevel) {
        $this->ifSTMTCondition = new AstExpression();
        $this->nestingLevel = $currentNestingLevel;
    }



}



