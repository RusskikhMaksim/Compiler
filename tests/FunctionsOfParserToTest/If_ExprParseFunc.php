<?php
function ifStatementNode($previousNonterminal, $currentParent, $currentToken, $testObj, $nestingLevelCounter)
{
    $ifStatementNode = new AstIfClass($nestingLevelCounter);

    if (isset($currentParent->childNode) && ($ifStatementNode->nestingLevel > $currentParent->nestingLevel)) {
        $previousNonterminal->nextNode = $ifStatementNode;
        $ifStatementNode->parentNode = $currentParent;
    } elseif (isset($currentParent->childNode) && ($ifStatementNode->nestingLevel < $currentParent->nestingLevel)) {
        while ($ifStatementNode->nestingLevel < $currentParent->nestingLevel) {
            $currentParent = $currentParent->parentNode;
        }
        $currentParent->nextNode = $ifStatementNode;
        $ifStatementNode->parentNode = $currentParent->parentNode;
    } elseif (isset($currentParent->childNode) && $ifStatementNode->nestingLevel === $currentParent->nestingLevel) {
        $currentParent->nextNode = $ifStatementNode;
        $ifStatementNode->parentNode = $currentParent->parentNode;
    } else {
        $currentParent->childNode = $ifStatementNode;
        $ifStatementNode->parentNode = $currentParent;
    }


    if ($currentToken->bodyOfToken === "if") {
        $ifStatementNode->typeOfNode = "conditional jump operator if";
        $ifStatementNode->bodyOfNode = "if";

        $currentToken = getNextToken();

        if ($currentToken->bodyOfToken === "(") {
            $ifStatementNode = expressionNode($ifStatementNode, $currentToken, $testObj, $nestingLevelCounter);

        }
        return $ifStatementNode;
    } elseif ($currentToken->bodyOfToken === "else") {
        $ifStatementNode->typeOfNode = "conditional jump operator else";
        $ifStatementNode->bodyOfNode = "else";

        $currentToken = getNextToken();

        if ($currentToken->bodyOfToken === "{") {
            return $ifStatementNode;

        }

    }
}

function expressionNode($previousNonterminal, $currentToken, $testObj, $nestingLevelCounter): object
{
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
    if ($previousNonterminal->typeOfNode === "Array declaration and initialization") {
        $previousNonterminal->expressionOrInitialize->typeOfExpression = "Initialization of array";

        //пропускаем скобку и знак равно
        if ($currentToken->tokenClass === "r_sqparen") {
            $currentToken = NextToken($testObj);
        }
        $currentToken = NextToken($testObj);

        if ($currentToken->bodyOfToken === "{") {
            $currentToken = NextToken($testObj);
            while ($currentToken->bodyOfToken !== "}") {
                //if($currentToken->tokenClass === "id" || $currentToken->tokenClass === $expectedTypeOfInputData) {

                $partsOfExpr[] = array(
                    "type of data" => "$currentToken->tokenClass",
                    "data" => $currentToken->bodyOfToken
                );
                //}
                //if($currentToken->bodyOfToken === ","){
                $//    $currentToken = NextToken($testObj);
                //    continue;
                //}
                $currentToken = NextToken($testObj);
            }

            $previousNonterminal->expressionOrInitialize->partsOfExpression = $partsOfExpr;
            return $previousNonterminal;
        }


    } //инициализация переменной
    elseif ($previousNonterminal->typeOfNode === "Variable declaration and initialization" || $previousNonterminal->typeOfNode === "Variables declaration and initialization") {
        $previousNonterminal->expressionOrInitialize->typeOfExpression = "Initialization of id";
        $currentToken = NextToken($testObj);
        print_r($currentToken);
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
                "data" => $currentToken->bodyOfToken
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


    } //выражение в качестве части операции присваивания
    elseif ($previousNonterminal->typeOfNode === "Variable assignment expression") {
        $previousNonterminal->dataToBeAssigned->typeOfExpression = "Variable assignment expression";
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
        $previousNonterminal->dataToBeAssigned->partsOfExpression = $partsOfExpr;
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
        array_pop($partsOfExpr);
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