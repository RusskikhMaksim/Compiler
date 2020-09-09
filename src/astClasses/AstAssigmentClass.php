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
}