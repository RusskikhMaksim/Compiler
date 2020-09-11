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

    public function printNode(){
        $printLVL ="";

        for($i = 0; $i <= $this->nestingLevel; $i++){
            $printLVL .= "-";
        }
        $printLVL .= ">";

        $printKeyWordNode = $printLVL . "[ Type: " . $this->typeOfNode . " ]\n";
        print($printKeyWordNode);

        $printKeyWord = $printLVL . "\t[ Type: KeyWord" . ", value: " . "'" . $this->bodyOfNode . "'" . " ]\n";
        print($printKeyWord);

        $printReturnValue = $printLVL . "\t[ Type: return value" . ", value: " . "'" . $this->returnValue . "'" . " ]\n";
        print($printReturnValue);
    }



}