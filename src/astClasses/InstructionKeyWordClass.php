<?php


class InstructionKeyWordClass extends AstNode
{
    public string $typeOfNode = "";
    public string $bodyOfNode = "";
    public string $returnValue;
    public object $parentNode;
    public object $childNode;
    public object $nextNode;
    public int $nestingLevel;

    public function __construct($currentNestingLevel) {
        $this->nestingLevel = $currentNestingLevel;
    }




}