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
            if ($declaredNode->isArray !== ""){
                $checkRow = explode("[", $symbol);
                if ($checkRow[0] === $declaredNode->isArray){
                    $symbolInTable->isArray = $declaredNode->isArray;
                }
            }

            if(is_object($symbolInTable)){
                $subTable->rowsOfTable[] = clone($symbolInTable);
            }

        }


    }

    public function setValueOfVariable($id, $valueOfId, $typeOfId){

        foreach ($this->rowsOfTable as $row) {
            $checkRow = explode("[", $row->nameOfId);
            //var_dump($checkRow);
            if ($checkRow[0] === $id) {
                if ($row->datatype === $typeOfId){
                    $row->valueOfId = $valueOfId;
                    return TRUE;
                } else {
                    //var_dump($row->datatype);
                    throw new Exception("unexpected datatype of '$id', actual datatype '$typeOfId'");
                }
            }
        }
        if (isset($this->parentTable->rowsOfTable)) {
            //var_dump($this);
            $value = $this->parentTable->setValueOfVariable($id, $valueOfId, $typeOfId);
            return $value;
        }
        return FALSE;
    }

    public function getValueOfVariable($id)
    {
        foreach ($this->rowsOfTable as $row) {
            $value = 0;
            $checkRow = explode("[", $row->nameOfId);
            //var_dump($checkRow);
            if ($checkRow[0] === $id) {
                $value = $row->valueOfId;
                return $value;
            }
        }
        if (isset($this->parentTable->rowsOfTable)) {
            //var_dump($this);
            $value = $this->parentTable->getValueOfVariable($id);
            return $value;
        }
        return FALSE;
    }

    public function getTypeOfVariable($id)
    {
        foreach ($this->rowsOfTable as $row) {
            $type = 0;
            $checkRow = explode("[", $row->nameOfId);
            //var_dump($checkRow);
            if ($checkRow[0] === $id) {
                $type = $row->datatype;
                return $type;
            }
        }
        if (isset($this->parentTable->rowsOfTable)) {
            //var_dump($this);
            $type = $this->parentTable->getTypeOfVariable($id);
            return $type;
        }
        return FALSE;
    }


    public function checkIfExists($datatype, $id){
        if ($datatype === ""){
            $existsOrNot = FALSE;
            foreach ($this->rowsOfTable as $row){
                $checkRow = explode("[", $row->nameOfId);
                /*
                if($id === "x"){
                    echo "id - ";
                    var_dump($id);
                    echo ("row of table - ");
                    var_dump($checkRow[0]);
                }*/

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
                $checkRow = explode("[", $row->nameOfId);
                if($row->datatype === $datatype && $checkRow[0] === $id){
                    throw new RedifinationException("redifination of $datatype $id");
                }
            }
        }
        return FALSE;
    }
  public function checkIfArray($id){

          $arrayOrNot = FALSE;
          foreach ($this->rowsOfTable as $row){
              if($row->isArray !== ""){
                  $checkRow = $row->isArray;
                  if($checkRow === $id){
                      return TRUE;
                  }
              }
          }
          if(isset($this->parentTable->rowsOfTable)){
              //var_dump($this);
              $arrayOrNot = $this->parentTable->checkIfArray($id);
          }
          return $arrayOrNot;
  }

}