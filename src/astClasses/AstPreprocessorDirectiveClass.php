<?php
require_once 'include.php';

class AstPreprocessorDirectiveClass extends AstNode
{
    public string $typeOfNode = "";
    public string $bodyOfNode = "";
    public object $parentNode;
    public object $childNode;

    public object $nextNode;
    public int $nestingLevel;

    public function __construct($currentNestingLevel)
    {
        $this->childNode = new AstNode($currentNestingLevel);

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

        $printPreProcDirective = $printLVL . "\t[ Type:" . $this->childNode->typeOfNode . ", value: " . "'" . $this->childNode->bodyOfNode . "'" . " ]\n";
        print($printPreProcDirective);

        $printCalleeLib = $printLVL . "\t[ Type:" . $this->childNode->nextNode->typeOfNode . ", value: " . "'" . $this->childNode->nextNode->bodyOfNode . "'" . " ]\n";
        print($printCalleeLib);
    }





}