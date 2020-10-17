<?php
//declare(strict_types=1);

class IdClass
{   //атрибуты типа данных и имени переменной либо функции
    public string $dataType = "";
    public string $id;
    public array $listOfDeclaredVariables;
    public string $typeOfNode;


    public function __construct()
    {
        $this->listOfDeclaredVariables = array();

    }

    //будет ли далее в строке инициализация либо парсим простое объявление
    public bool $declareWithInitialize = FALSE;
    //атрибуты для объявления и инициализации массива
    public string $sizeOfArray;
    public string $typeOfArraySizeValue = "";



}