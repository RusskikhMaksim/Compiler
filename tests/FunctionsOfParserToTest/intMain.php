<?php
function declareFuncOrId(object $previousNonterminal,object $currentParent, object $currentToken, $testObj, $nestingLevelCounter): object{
    $declarationNode = new AstFuncAndIdClass($nestingLevelCounter);
    $datatypeOfNonterminal = $currentToken->bodyOfToken;
    $currentToken = NextToken($testObj);
    $idOfNonterminal = $currentToken->bodyOfToken;

    $declarationNode->dataTypeAndId->id = $idOfNonterminal;
    $declarationNode->dataTypeAndId->dataType = $datatypeOfNonterminal;
    $declarationNode->parentNode = $currentParent;

    if (isset($currentParent->childNode) && ($declarationNode->nestingLevel > $currentParent->nestingLevel)) {
        $previousNonterminal->nextNode = $declarationNode;
        $declarationNode->parentNode = $currentParent;
    } elseif (isset($currentParent->childNode) && ($declarationNode->nestingLevel < $currentParent->nestingLevel)){
        while ($declarationNode->nestingLevel < $currentParent->nestingLevel) {
            $currentParent = $currentParent->parentNode;
        }
        $currentParent->nextNode = $declarationNode;
        $declarationNode->parentNode = $currentParent->parentNode;
    } elseif (isset($currentParent->childNode) && $declarationNode->nestingLevel === $currentParent->nestingLevel) {
        $currentParent->nextNode = $declarationNode;
        $declarationNode->parentNode = $currentParent->parentNode;
    } else {
        $currentParent->childNode = $declarationNode;
        $declarationNode->parentNode = $currentParent;
    }

    //defineLinksBetweenNodes($previousNonterminal, $currentParent, $declarationNode);

    /*if(isset($currentParent->childNode)){
        $previousNonterminal->nextNode = $declarationNode;
    }
    else{
        $currentParent->childNode = $declarationNode;
    }*/


    $currentToken = NextToken($testObj);

    //объявление функции
    if($currentToken->bodyOfToken === "(") {

        $declarationNode->typeOfNode = "Function declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and function name";

        while ($currentToken->bodyOfToken !==")"){
            $currentToken = NextToken($testObj);
        }

        $currentToken = NextToken($testObj);
        if($currentToken->bodyOfToken === "{"){
            return $declarationNode;
        }
        else{
            printf("missed \"{\" on pos %d in str %d", $currentToken->startPositionInString, $currentToken->NumOfStringInProgram);
            return $declarationNode;
        }
    }

    //объявление переменной
    if($currentToken->bodyOfToken === ";"){
        $declarationNode->typeOfNode = "Variable declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of variable";

        return $declarationNode;

    }
    //объявление массива
    if($currentToken->bodyOfToken === "["){
        $declarationNode->typeOfNode = "Array declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of array";

        $currentToken = NextToken($testObj);
        //если полученный токен - число либо переменная, то указывается размерность массива
        if($currentToken->tokenClass === "numeric_constant" || $currentToken->tokenClass === "id"){
            $declarationNode->dataTypeAndId->sizeOfArray = $currentToken->bodyOfToken;
            $currentToken = NextToken($testObj);
            if($currentToken->tokenClass !== "r_sqparen"){
                //пропущена закрывающая квадратная скобка
                printf("missed \"]\" on pos %d in str %d", $currentToken->startPositionInString, $currentToken->NumOfStringInProgram);
            }
            $currentToken = NextToken($testObj);
            //только объявление массива
            if($currentToken->bodyOfToken === ";"){
                $declarationNode->dataTypeAndId->declareWithInitialize = FALSE;
                return $declarationNode;
            }
            //инициализация, обрабатывается далее отдельно
            elseif($currentToken->bodyOfToken === "="){
                $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
                return $declarationNode;
            }
        }
        //если кв. скобка, значит размерность опускается, далее будет инициализация
        elseif($currentToken->tokenClass === "r_sqparen"){
            $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
            return $declarationNode;
        }
    }


    //объявление переменной
    elseif($currentToken->bodyOfToken === "="){
        $declarationNode->typeOfNode = "variable declaration and initialization";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of variable";
        $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
        return $declarationNode;
        //функция парсинга выражений
    }
}

/*
function defineLinksBetweenNodes($previousNonterminal, $currentParent, $currentNonterminal){


}*/






/* OLD_VERSION
function declareFuncOrId(object $previousNonterminal,object $currentParent, object $currentToken, $testObj): object{
    $declarationNode = new astFuncAndIdClass();
    $datatypeOfNonterminal = $currentToken->bodyOfToken;
    $currentToken = NextToken($testObj);
    $idOfNonterminal = $currentToken->bodyOfToken;

    $declarationNode->dataTypeAndId->id = $idOfNonterminal;
    $declarationNode->dataTypeAndId->dataType = $datatypeOfNonterminal;
    $declarationNode->parentNode = $currentParent;
    if(isset($currentParent->childNode)){
        $previousNonterminal->nextNode = $declarationNode;
    }
    else{
        $currentParent->childNode = $declarationNode;
    }


    $currentToken = NextToken($testObj);

    //объявление функции
    if($currentToken->bodyOfToken === "(") {

        $declarationNode->typeOfNode = "Function declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and function name";

        while ($currentToken->bodyOfToken !==")"){
            $currentToken = NextToken($testObj);
        }

        $currentToken = NextToken($testObj);
        if($currentToken->bodyOfToken === "{"){
            return $declarationNode;
        }
        else{
            printf("missed \"{\" on pos %d in str %d", $currentToken->startPositionInString, $currentToken->NumOfStringInProgram);
            return $declarationNode;
        }
    }

    //объявление переменной
    if($currentToken->bodyOfToken === ";"){
        $declarationNode->typeOfNode = "Variable declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of variable";

        while($currentToken->bodyOfToken !==")"){
            $currentToken = NextToken($testObj);
        }

        $currentToken = NextToken($testObj);
        if($currentToken->bodyOfToken === "{"){
            return $declarationNode;
        }
        else{
            printf("missed \"{\" on pos %d in str %d", $currentToken->startPositionInString, $currentToken->NumOfStringInProgram);
            return $declarationNode;
        }
    }

    //объявление переменной с инициализацией
    elseif($currentToken->bodyOfToken === "="){
        $declarationNode->typeOfNode = "variable declaration and initialization";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of variable";
        //функция парсинга выражений
    }
}*/


/*
function NextToken($obj) {
    //global $Token;
    //global $tokenArrayIndex;

    return $obj->Token[$obj->tokenArrayIndex++];

}*/

