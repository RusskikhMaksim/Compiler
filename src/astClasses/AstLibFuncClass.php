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


    public function printNode(){
        $printLVL ="";

        for($i = 0; $i <= $this->nestingLevel; $i++){
            $printLVL .= "-";
        }
        $printLVL .= ">";

        $printInputOrOutputNode = $printLVL . "[ Type: " . $this->typeOfNode . " ]\n";
        print($printInputOrOutputNode);

        $printNameOfFunc = $printLVL . "\t[ Type: callee function" . ", value: " . "'" . $this->bodyOfNode . "'" . " ]\n";
        print($printNameOfFunc);

        $printCallableArguments = $printLVL . "\t[ Type: callable arguments" . " ]\n";
        print($printCallableArguments);

        foreach ($this->callableArguments as $value){

            $printData = $printLVL . "\t\t[ Type: part of " . $this->bodyOfNode . " format" . ", data: " . "'" .$value . "' ]\n";
            print($printData);
        }
    }




}