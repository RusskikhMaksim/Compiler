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

    public function setDataType(string $dataType): void {
        $this->dataTypeAndId->dataType = $dataType;
    }
    public function getDataType(): string {
        return $this->dataTypeAndId->dataType;
    }
    public function setId(string $id): void{
        $this->dataTypeAndId->id = $id;
    }
    public function getId(): string {
        return $this->dataTypeAndId->id;
    }

    public function setFunctionArguments(array $functionArguments): void {
        $this->functionArguments = $functionArguments;
    }

    public function getFunctionArguments(): array {
        return $this->functionArguments;
    }


    public function setTypeOfNode(string $typeOfNode): void {
        $this->typeOfNode = $typeOfNode;

    }

    public function getTypeOfNode(): string {
        return $this->typeOfNode;
    }

    public function setParentNode(object $parentNode): void {
        $this->parentNode = $parentNode;
    }

    public function getParentNode(): object{
        return $this->parentNode;
    }

    public function setBodyOfFunction(object $childNode): void {
        $this->childNode = $childNode;
    }

    public function getBodyOfFunction(): object{
        return $this->childNode;
    }

    public function setNextNode(object $nextNode): void {
        $this->nextNode = $nextNode;
    }

    public function getNextNode(): object{
        return $this->nextNode;
    }


}

