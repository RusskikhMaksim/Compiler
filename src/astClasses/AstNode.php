<?php


class AstNode
{
    public string $typeOfNode = "";
    public string $bodyOfNode = "";
    public object $parentNode;
    public object $childNode;
    public object $nextNode;
    public int $nestingLevel;

    public function __construct($currentNestingLevel) {
        $this->nestingLevel = $currentNestingLevel;
    }

    public function setTypeOfNode(string $typeOfNode): void {
        $this->typeOfNode = $typeOfNode;

    }

    public function getTypeOfNode(): string {
        return $this->typeOfNode;
    }

    public function setBodyOfNode(string $bodyOfNode): void {
        $this->bodyOfNode = $bodyOfNode;
    }

    public function getBodyOfNode(): string {
        return $this->bodyOfNode;
    }


    public function setParentNode(object $parentNode): void {
        $this->parentNode = $parentNode;
    }

    public function getParentNode(): object{
        return $this->parentNode;
    }

    public function setChildNode(object $childNode): void {
        $this->childNode = $childNode;
    }

    public function getChildNode(): object{
        return $this->childNode;
    }

    public function setNextNode(object $nextNode): void {
        $this->nextNode = $nextNode;
    }

    public function getNextNode(): object{
        return $this->nextNode;
    }

}