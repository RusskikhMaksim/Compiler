<?php
require_once 'include.php';

class AstPreprocessorDirectiveClass extends AstNode
{
    public string $typeOfNode = "";
    public string $bodyOfNode = "";
    public object $parentNode;
    public object $childNode;
    public string $directive = "";
    public string $calleeLib = "";
    public object $nextNode;
    public int $nestingLevel;

    public function __construct($currentNestingLevel)
    {
        $this->nestingLevel = $currentNestingLevel;
    }

    public function printNode(){
        $printLVL ="";

        for($i = 0; $i <= $this->nestingLevel; $i++){
            $printLVL .= "-";
        }
        $printLVL .= ">";

        $printPreProcNode = $printLVL . "[ Type: " . $this->typeOfNode . " ]\n";
        print($printPreProcNode);

        $printPreProcDirective = $printLVL . "\t[ Type: preprocessor directive, value: " . "'" . $this->directive . "'" . " ]\n";
        print($printPreProcDirective);

        $printCalleeLib = $printLVL . "\t[ Type: callee library, value: " . "'" . $this->calleeLib . "'" . " ]\n";
        print($printCalleeLib);
    }





}