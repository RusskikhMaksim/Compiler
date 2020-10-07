<?php
require_once "SymbolTableRowClass.php";

class SymbolTableClass
{
    public array $rowsOfTable = [];
    public object $parentTable;


    public function __construct($parentTable)
    {
        $this->parentTable = $parentTable;
    }

    public function setVariable($subTable, $declaredNode){
        foreach ($declaredNode->dataTypeAndId->listOfDeclaredVariables as $symbol){
            if($symbol === ","){
                continue;
            }
            $symbolInTable = new SymbolTableRowClass();
            $symbolInTable->datatype = $declaredNode->dataTypeAndId->dataType;
            $symbolInTable->nameOfId = $symbol;
            $symbolInTable->scope = $declaredNode->nestingLevel;
            $symbolInTable->NumOfStringInProgram = $declaredNode->NumOfStringInProgram;

            if(is_object($symbolInTable)){
                $subTable->rowsOfTable[] = clone($symbolInTable);
            }

        }
    }
    public function getVariable(){}
    public function checkIfExists($datatype, $id){
        if ($datatype === ""){
            $existsOrNot = FALSE;
            foreach ($this->rowsOfTable as $row){
                $checkRow = explode("[", $row->nameOfId);
                //var_dump($checkRow);
                if( $checkRow[0] === $id){
                    $existsOrNot = TRUE;
                    return $existsOrNot;
                }
            }
            if(isset($this->parentTable->rowsOfTable)){
                //var_dump($this);
                $existsOrNot = $this->parentTable->checkIfExists($datatype, $id);
            }
            return $existsOrNot;


        } else {
            //var_dump($this->parentTable);
            foreach ($this->rowsOfTable as $row){
                if($row->datatype === $datatype && $row->nameOfId === $id){
                    throw new RedifinationException("redifination of $datatype $id");
                }
            }
        }
        return FALSE;
    }

}