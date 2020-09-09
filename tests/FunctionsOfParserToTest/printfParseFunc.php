<?php
function functionCallOrVariableAssignment($previousNonterminal, $currentParent, $currentToken, $testObj, $nestingLevelCounter): object
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

        while ($currentToken->bodyOfToken !== ")") {
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


    if (isset($currentParent->childNode)) {
        $previousNonterminal->nextNode = $calleeFunction;
    } else {
        $currentParent->childNode = $calleeFunction;
    }

    $calleeFunction->parentNode = $currentParent;

    return $calleeFunction;
    //переменная
    //объявление
    //присваивание
    // через выражение
    // через инкремент декремент
}