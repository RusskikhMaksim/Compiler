<?php
require_once 'IdClass.php';
require_once 'AstExpression.php';


class AstDeclarationClass extends AstNode
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
   public string $isArray = "";
   public object $symbolTable;
   public int $NumOfStringInProgram = 0;

    public function __construct($currentNestingLevel) {
        $this->dataTypeAndId = new IdClass();
        $this->expressionOrInitialize = new AstExpression();
        $this->nestingLevel = $currentNestingLevel;
    }

    public function printNode(){
        $printLVL ="";
        for($i = 0; $i <= $this->nestingLevel; $i++){
            $printLVL .= "-";
        }
        $printLVL .= ">";

        if($this->dataTypeAndId->declareWithInitialize){

            if($this->typeOfNode === "Variable declaration and initialization"){
                $printVariable = $printLVL . "[ Type: " . $this->typeOfNode . ", '" . $this->dataTypeAndId->id . "' ]\n";
                print($printVariable);
                $printInit = $printLVL . "\t[ Type: expression of initialize ]\n";
                print($printInit);
                foreach ($this->expressionOrInitialize->partsOfExpression as $value){
                    $printInitializeExpression = $printLVL . "\t\t[ Type: " . $value["type of data"] . ", value: '" . $value["data"] . "' ]\n";
                    print($printInitializeExpression);
                }
            }

            if($this->typeOfNode === "Variables declaration and initialization"){
                $printVariable = $printLVL . "[ Type: " . $this->typeOfNode .  " ]\n";
                print($printVariable);
                foreach ($this->dataTypeAndId->listOfDeclaredVariables as $value){
                    if($value === ","){
                        continue;
                    }
                    $printInitializeExpression = $printLVL . "\t\t[ Type: declared variables" . ", value: '" . $value . "' ]\n";
                    print($printInitializeExpression);
                }
                $printVariable = $printLVL . "\t[ Type: variable to initialize, " . "'" .
                    $this->dataTypeAndId->listOfDeclaredVariables[array_key_last($this->dataTypeAndId->listOfDeclaredVariables)] . "' ]\n";
                print($printVariable);

                $printInit = $printLVL . "\t[ Type: expression of initialize, value: \"=\" ]\n";
                print($printInit);
                foreach ($this->expressionOrInitialize->partsOfExpression as $value){
                    if($value === ","){
                        continue;
                    }
                    $printInitializeExpression = $printLVL . "\t\t[ Type: " . $value["type of data"] . ", value: '" . $value["data"] . "' ]\n";
                    print($printInitializeExpression);
                }
            }

            if($this->typeOfNode === "Array declaration and initialization"){
                $printArray = $printLVL . "[ Type: " . $this->typeOfNode . ", value: '" . $this->dataTypeAndId->id . "', size of array:" . $this->dataTypeAndId->sizeOfArray ." ]\n";
                print($printArray);
                $printInit = $printLVL . "\t[ Type: expression of initialize ]\n";
                print($printInit);

                foreach ($this->expressionOrInitialize->partsOfExpression as $value){
                    if($value["data"] === ","){
                        continue;
                    }
                    $printInitializeExpression = $printLVL . "\t\t[ Type: " . $value["type of data"] . ", value: '" . $value["data"] . "' ]\n";
                    print($printInitializeExpression);
                }
            }
        }
        else{
            //мейн
            //переменная
            //массив
            //несколько переменных
            if($this->typeOfNode === "Function declaration") {
                $printFuncDeclarationNode = $printLVL . "[ Type: " . $this->typeOfNode . ", value: " . "'" . $this->dataTypeAndId->id . "' ]\n";
                print($printFuncDeclarationNode);
            }

            if($this->typeOfNode === "Variable declaration") {
                $printVariable = $printLVL . "[ Type: " . $this->typeOfNode . ", '" . $this->dataTypeAndId->id . "' ]\n";
                print($printVariable);
            }

            if($this->typeOfNode === "Variables declaration"){
                $printVariable = $printLVL . "[ Type: " . $this->typeOfNode . " ]\n";
                print($printVariable);

                foreach ($this->dataTypeAndId->listOfDeclaredVariables as $value){
                    if($value === ","){
                        continue;
                    }
                    $printVariable = $printLVL . "\t[ Type: The variable being declared" . ", value: '" . $value . "' ]\n";
                    print($printVariable);
                }
            }

            if($this->typeOfNode === "Array declaration"){
                $printArray = $printLVL . "[ Type: " . $this->typeOfNode . ", value: '" . $this->dataTypeAndId->id . "', size of array:" . $this->dataTypeAndId->sizeOfArray ." ]\n";
                print($printArray);

            }
        }
    }


}

