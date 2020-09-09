<?php
require_once 'include.php';
require_once 'AstExpression.php';

class AstWhileClass extends AstNode
{
    public string $typeOfNode = "";
    public object $loopCondition;
    public object $parentNode;
    //начало тела цикла
    public object $childNode;
    public object $nextNode;

    public function __construct($currentNestingLevel) {
        $this->loopCondition = new AstExpression();
        $this->nestingLevel = $currentNestingLevel;

    }





}