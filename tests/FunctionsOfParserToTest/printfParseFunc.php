<?php
function inputOrOutputNode($previousNonterminal, $currentParent, $currentToken, $testObj, $nestingLevelCounter): object
{
    //функция
    $calleeFunction = new AstLibFuncClass($nestingLevelCounter);
    $calleeFunction->typeOfNode = "Calling a library function";
    $calleeFunction->bodyOfNode = $currentToken->bodyOfToken;

    //пропускаем открывающую скобку
    NextToken($testObj);
    $currentToken = NextToken($testObj);
    //формат
    $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
    NextToken($testObj);
    $currentToken = NextToken($testObj);


    if ($calleeFunction->bodyOfNode === "printf") {

        //переменные для вывода

        while ($currentToken->bodyOfToken !== ")" && $currentToken->bodyOfToken !== ";") {

            //добавляем запятые в качестве разделителя в массив
            if ($currentToken->bodyOfToken === ",") {
                $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
                $currentToken = NextToken($testObj);
                continue;
            }
            if ($currentToken->tokenClass === "id" || $currentToken->tokenClass === "numeric_constant" || $currentToken->tokenClass === "l_sqparen" || $currentToken->tokenClass === "r_sqparen") {
                //выводится переменная либо массив
                $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
                $currentToken = NextToken($testObj);
            }
        }
    } elseif ($calleeFunction->bodyOfNode === "scanf") {

        //переменные для ввода

        while ($currentToken->bodyOfToken !== ")") {
            //добавляем запятые в качестве разделителя в массив
            if ($currentToken->bodyOfToken === ",") {
                $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
                $currentToken = NextToken($testObj);
                continue;
            }
            if ($currentToken->tokenClass === "bitwise_and") {
                $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
                $currentToken = NextToken($testObj);
            }
            if ($currentToken->tokenClass === "id" || $currentToken->tokenClass === "l_sqparen" || $currentToken->tokenClass === "r_sqparen") {
                //выводится переменная либо массив
                $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
                $currentToken = NextToken($testObj);
            }
        }
    }


    if (isset($currentParent->childNode) && ($calleeFunction->nestingLevel > $currentParent->nestingLevel)) {
        $previousNonterminal->nextNode = $calleeFunction;
        $calleeFunction->parentNode = $currentParent;
    } elseif (isset($currentParent->childNode) && ($calleeFunction->nestingLevel < $currentParent->nestingLevel)) {
        while ($calleeFunction->nestingLevel < $currentParent->nestingLevel) {
            $currentParent = $currentParent->parentNode;
        }
        $currentParent->nextNode = $calleeFunction;
        $calleeFunction->parentNode = $currentParent->parentNode;
    } elseif (isset($currentParent->childNode) && $calleeFunction->nestingLevel === $currentParent->nestingLevel) {
        $currentParent->nextNode = $calleeFunction;
        $calleeFunction->parentNode = $currentParent->parentNode;
    } else {
        $currentParent->childNode = $calleeFunction;
        $calleeFunction->parentNode = $currentParent;
    }


    return $calleeFunction;
    //переменная
    //объявление
    //присваивание
    // через выражение
    // через инкремент декремент
}