<?php


class AstExpression
{
    // инициализация массива или перменной или выражение как часть операции присваивания
    public string $typeOfExpression;
    //тело выражения
    public array $partsOfExpression;

    public function __construct()
    {
        $this->partsOfExpression = array();
    }
}