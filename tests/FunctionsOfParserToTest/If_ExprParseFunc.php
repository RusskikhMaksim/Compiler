<?php
function expressionNode($previousNonterminal, $currentToken, $testObj, $nestingLevelCounter){
    $arrayOfOperators = array(
        "-", "+", "*", "/", "%", "++", "--", "(", ")", "&&", "||",
        "!", "==", "!=", "<", ">", "<=", ">="
    );

    //инициализация массива
    $partsOfExpr = array();
    //$typeOfArrData = $previousNonterminal->dataTypeAndId->dataType;
    //if($typeOfArrData === "int") {
    //    $expectedTypeOfInputData = "numeric_constant";
    //}
    //elseif($typeOfArrData === "char"){
    //    $expectedTypeOfInputData = "string litheral";
    //}
    if($previousNonterminal->typeOfNode === "Array declaration and initialization"){
        $previousNonterminal->expressionOrInitialize->typeOfExpression = "Initialization of array";

        //пропускаем скобку и знак равно
        if($currentToken->tokenClass === "r_sqparen"){
            $currentToken = NextToken($testObj);
        }
        $currentToken = NextToken($testObj);

        if($currentToken->bodyOfToken === "{"){
            $currentToken = NextToken($testObj);
            while($currentToken->bodyOfToken !== "}"){
                //if($currentToken->tokenClass === "id" || $currentToken->tokenClass === $expectedTypeOfInputData) {

                    $partsOfExpr[] = array(
                        "type of data" => "$currentToken->tokenClass",
                        "element of array" => $currentToken->bodyOfToken
                    );
                //}
                //if($currentToken->bodyOfToken === ","){
                //    $currentToken = NextToken($testObj);
                //    continue;
                //}
                $currentToken = NextToken($testObj);
            }

            $previousNonterminal->expressionOrInitialize->partsOfExpression = $partsOfExpr;
            return $previousNonterminal;
        }


    }
    //инициализация переменной
    elseif($previousNonterminal->typeOfNode === "Variable declaration and initialization"){
        $previousNonterminal->expressionOrInitialize->typeOfExpression = "Initialization of id";
        $currentToken = NextToken($testObj);
        //если переменной присваивается char символ

        //если числовое выражение
        //elseif($currentToken->tokenClass === "int"){
            $strNum = $currentToken->NumOfStringInProgram;
            $isExprClosed = false;
            //считываем выражение
        if ($currentToken->tokenClass === "string litheral") {
            //if($currentToken->tokenClass === "string litheral"){
            $partsOfExpr[] = array(
                "type of data" => "char",
                "symbol" => $currentToken->bodyOfToken
            );
            //}
            //копируем собранное выражение в узел
            $previousNonterminal->expressionOrInitialize->partsOfExpression = $partsOfExpr;
            return $previousNonterminal;
        }

        while ($currentToken->bodyOfToken !== ";" && $currentToken->NumOfStringInProgram === $strNum) {
            $partsOfExpr[] = array(
                "type of data" => "$currentToken->tokenClass",
                "data" => "$currentToken->bodyOfToken"
            );

            $currentToken = NextToken($testObj);
        }
            //копируем собранное выражение в узел
            $previousNonterminal->expressionOrInitialize->partsOfExpression = $partsOfExpr;
            return $previousNonterminal;


    }
    //выражение в качестве части операции присваивания
    elseif($previousNonterminal->typeOfNode === "Variable assignment expression") {
        $previousNonterminal->expressionOrInitialize->typeOfExpression = "Variable assignment expression";
        $currentToken = NextToken($testObj);
        $endOfExpr = ";";

        //если числовое выражение
        $strNum = $currentToken->NumOfStringInProgram;
        $isExprClosed = false;
        //считываем выражение
        while ($currentToken->bodyOfToken !== $endOfExpr && $currentToken->NumOfStringInProgram === $strNum) {
            $partsOfExpr[] = array(
                "type of data" => "$currentToken->tokenClass",
                "data" => "$currentToken->bodyOfToken"
            );
            $currentToken = NextToken($testObj);
        }
        //копируем собранное выражение в узел
        $previousNonterminal->expressionOrInitialize->partsOfExpression = $partsOfExpr;
        return $previousNonterminal;
        
    } elseif ($previousNonterminal->typeOfNode === "while loop") {
        $previousNonterminal->loopCondition->typeOfExpression = "loop condition expression";

        $currentToken = NextToken($testObj);
        $endOfExpr = "{";
        $strNum = $currentToken->NumOfStringInProgram;
        $isExprClosed = false;

        while ($currentToken->bodyOfToken !== $endOfExpr && $currentToken->NumOfStringInProgram === $strNum) {
            $partsOfExpr[] = array(
                "type of data" => "$currentToken->tokenClass",
                "data" => "$currentToken->bodyOfToken"
            );
            $currentToken = NextToken($testObj);
        }

        $previousNonterminal->loopCondition->partsOfExpression = $partsOfExpr;
        return $previousNonterminal;
    } elseif ($previousNonterminal->typeOfNode === "conditional jump operator if") {
        $previousNonterminal->ifSTMTCondition->typeOfExpression = "conditional if expression";
        $currentToken = NextToken($testObj);
        $endOfExpr = "{";

        $strNum = $currentToken->NumOfStringInProgram;
        $isExprClosed = false;
        //считываем выражение
        while ($currentToken->bodyOfToken !== $endOfExpr && $currentToken->NumOfStringInProgram === $strNum) {
            $partsOfExpr[] = array(
                "type of data" => "$currentToken->tokenClass",
                "data" => "$currentToken->bodyOfToken"
            );

            $currentToken = NextToken($testObj);
        }

        //копируем собранное выражение в узел
        array_pop($partsOfExpr);
        $previousNonterminal->ifSTMTCondition->partsOfExpression = $partsOfExpr;
        return $previousNonterminal;
    }

    return $previousNonterminal;
}