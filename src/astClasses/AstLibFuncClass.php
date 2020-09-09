<?php


class AstLibFuncClass extends AstNode
{
    public string $typeOfNode = "";
    public object $parentNode;
    public object $childNode;
    public object $nextNode;
    public array $callableArguments;
    public int $nestingLevel;

    public function __construct($currentNestingLevel) {
        $this->nestingLevel = $currentNestingLevel;
    }





}