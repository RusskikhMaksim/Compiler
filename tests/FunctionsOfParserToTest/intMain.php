<?php
function declareSomething(object $previousNonterminal, object $currentParent, object $currentToken, $testObj, $nestingLevelCounter): object
{
    $declarationNode = new AstDeclarationClass($nestingLevelCounter);
    $datatypeOfNonterminal = $currentToken->bodyOfToken;
    $currentToken = NextToken($testObj);
    $idOfNonterminal = $currentToken->bodyOfToken;

    $declarationNode->dataTypeAndId->id = $idOfNonterminal;
    $declarationNode->dataTypeAndId->dataType = $datatypeOfNonterminal;
    $declarationNode->parentNode = $currentParent;
    if (isset($currentParent->childNode) && ($declarationNode->nestingLevel > $currentParent->nestingLevel)) {
        $previousNonterminal->nextNode = $declarationNode;
        $declarationNode->parentNode = $currentParent;
    } elseif (isset($currentParent->childNode) && ($declarationNode->nestingLevel < $currentParent->nestingLevel)) {
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


    $currentToken = NextToken($testObj);

    //объявление функции
    if ($currentToken->bodyOfToken === "(") {

        $declarationNode->typeOfNode = "Function declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and function name";

        while ($currentToken->bodyOfToken !== ")") {
            $currentToken = NextToken($testObj);
        }

        $currentToken = NextToken($testObj);
        if ($currentToken->bodyOfToken === "{") {
            return $declarationNode;
        } else {
            printf("missed \"{\" on pos %d in str %d", $currentToken->startPositionInString, $currentToken->NumOfStringInProgram);
            return $declarationNode;
        }
    }

    //объявление переменной
    if ($currentToken->bodyOfToken === ";") {
        $declarationNode->typeOfNode = "Variable declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of variable";

        return $declarationNode;

    } elseif ($currentToken->bodyOfToken === ",") {
        $declarationNode->typeOfNode = "Variables declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of variable";
        $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $declarationNode->dataTypeAndId->id;

        while ($currentToken->bodyOfToken !== ";") {
            if ($currentToken->bodyOfToken === "=") {
                $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
                $declarationNode->typeOfNode = "Variables declaration and initialization";
                return $declarationNode;
            }
            if ($currentToken->bodyOfToken === "[") {
                $arrayLexeme = "";
                for ($i = 0; $i < 3; $i++) {
                    $arrayLexeme .= $currentToken->bodyOfToken;
                    $currentToken = NextToken($testObj);
                }
                $declarationNode->dataTypeAndId->listOfDeclaredVariables[array_key_last($declarationNode->dataTypeAndId->listOfDeclaredVariables)] .= $arrayLexeme;
                continue;
            }
            $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $currentToken->bodyOfToken;
            $currentToken = NextToken($testObj);
        }

        return $declarationNode;
    }

    //объявление массива
    if ($currentToken->bodyOfToken === "[") {
        $arrayLexeme = $declarationNode->dataTypeAndId->id;
        $declarationNode->typeOfNode = "Array declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of array";
        $arrayLexeme .= $currentToken->bodyOfToken;
        $currentToken = NextToken($testObj);
        $arrayLexeme .= $currentToken->bodyOfToken;
        //если полученный токен - число либо переменная, то указывается размерность массива
        if ($currentToken->tokenClass === "numeric_constant" || $currentToken->tokenClass === "id") {
            $declarationNode->dataTypeAndId->sizeOfArray = $currentToken->bodyOfToken;
            $currentToken = NextToken($testObj);
            $arrayLexeme .= $currentToken->bodyOfToken;
            if ($currentToken->tokenClass !== "r_sqparen") {
                //пропущена закрывающая квадратная скобка
                printf("missed \"]\" on pos %d in str %d", $currentToken->startPositionInString, $currentToken->NumOfStringInProgram);
            }
            $currentToken = NextToken($testObj);
            //только объявление массива
            if ($currentToken->bodyOfToken === ";") {
                $declarationNode->dataTypeAndId->declareWithInitialize = FALSE;
                return $declarationNode;
            } //инициализация, обрабатывается далее отдельно
            elseif ($currentToken->bodyOfToken === "=") {
                $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
                $declarationNode->typeOfNode = "Array declaration and initialization";
                return $declarationNode;
            } elseif ($currentToken->bodyOfToken === ",") {
                $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $arrayLexeme;


                while ($currentToken->bodyOfToken !== ";") {
                    if ($currentToken->bodyOfToken === "=") {
                        $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
                        $declarationNode->typeOfNode = "Variables declaration and initialization";
                        return $declarationNode;
                    }
                    if ($currentToken->bodyOfToken === "[") {
                        $arrayLexeme = "";
                        for ($i = 0; $i < 3; $i++) {
                            $arrayLexeme .= $currentToken->bodyOfToken;
                            $currentToken = NextToken($testObj);
                        }
                        $declarationNode->dataTypeAndId->listOfDeclaredVariables[array_key_last($declarationNode->dataTypeAndId->listOfDeclaredVariables)] .= $arrayLexeme;
                        continue;
                    }
                    $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $currentToken->bodyOfToken;
                    $currentToken = NextToken($testObj);
                }

                return $declarationNode;
            }

        } //если кв. скобка, значит размерность опускается, далее будет инициализация
        elseif ($currentToken->tokenClass === "r_sqparen") {
            $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
            $declarationNode->typeOfNode = "Array declaration and initialization";
            return $declarationNode;
        }
    } //объявление переменной и её инициализация
    elseif ($currentToken->bodyOfToken === "=") {
        $declarationNode->typeOfNode = "Variable declaration and initialization";
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

