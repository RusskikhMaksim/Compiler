<?php
/*
 * TO DO:
 *  сделать функцию проверки токена в выражении - если неожиданный, ошибка
 */
function evaluationOfAnExpression($exprNode, $typeOfExpr){
    if ($typeOfExpr === "assigment"){
        $exprInRPN = [];
        $exprInRPN = marshallingYard($exprNode, $typeOfExpr);
    }




}


function marshallingYard($exprNode, $typeOfExpr){
    $operatorsStack = [];
    $outputArr = [];

    $arrayOfOperators = array(
        "-", "+", "*", "/", "%", "(", ")"
    );

    $arrayOfLogicOperators = array(
     "&&", "||", "!", "==", "!=", "<", ">", "<=", ">="
    );

    $dataTypeOfExpr = $exprNode->expressionOrInitialize->typeOfExpression;
    $expression = $exprNode->expressionOrInitialize->partsOfExpression;

    foreach ($exprNode->expressionOrInitialize->partsOfExpression as $part) {
        if($part["type of data"] === "numeric_constant"){
            //кидаем в выходной массив
            $outputArr[] = $part["data"];

        } elseif ($part["type of data"] === "id"){
            //проверяем, существует ли переменная и есть ли у неё значение
        } elseif (in_array($part["data"], $arrayOfOperators)) {

        }
    }

    return $outputArr;
}