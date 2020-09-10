<?php
require_once 'IdClass.php';
require_once 'AstExpression.php';


class AstFuncAndIdClass extends AstNode
{
   public object $dataTypeAndId;
   public object $expressionOrInitialize;
   public string $typeOfNode = "";
   public array $functionArguments;
   public object $childNode;
   public object $parentNode;
   public object $nextNode;
   public object $assigmentExpr;
   public int $nestingLevel;

    public function __construct($currentNestingLevel) {
        $this->dataTypeAndId = new IdClass();
        $this->expression = new astExpression();
        $this->nestingLevel = $currentNestingLevel;
    }

    public function printNode(){
        $printLVL ="";
        for($i = 0; $i <= $this->nestingLevel; $i++){
            $printLVL .= "  ";
        }

        if($this->dataTypeAndId->declareWithInitialize){

        }
        else{
            //мейн
            //переменная
            //массив
            //несколько переменных
            $printDeclarationNode = $printLVL . "[ Type: " . $this->typeOfNode . ", value: " . "'" . $this->dataTypeAndId->id . "' ]\n";
            print($printDeclarationNode);

            $printVariable = $printLVL . "\t[ Type: variable to assigning, " . "'" . $this->variableToAssigning->id . "' ]\n";
            print($printVariable);
        }
    }


}

