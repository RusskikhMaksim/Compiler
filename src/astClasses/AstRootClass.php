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




}