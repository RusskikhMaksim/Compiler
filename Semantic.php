<?php
/*
 * TO DO:
 *  сделать функцию проверки токена в выражении - если неожиданный, ошибка
 * реализовать установку значений переменным в таблицу символов
 * придумать, как быть со сканефом
 * придумать, как быть с массивом
 * сделать нормальный вывод ошибок семантики
 */
require_once 'include.php';
$handler = fopen("tests/FindMinArrayElement.c", "r");
$tokenArr = array();
$Lexer = 'myLexer';
$Token = $Lexer($handler, $tokenArr);
$tokenArrayIndex = 0;
$currentNonterminal;
$currentParent;
$nestingLevelCounter = 0;

$syntaxErrorHandler = new SyntaxErrorHandler($Token);
$nullObj = new stdClass();
$symbolTable = new SymbolTableClass($nullObj);
$currentTable = $symbolTable;
$subTable = $currentTable;

$ast = new AstClass($nestingLevelCounter);
$root = new AstRootClass($nestingLevelCounter);
$root->setTypeOfNode("Program");
$ast->childNode = $root;
//$ast->printNode();
$root->parentNode = $ast;
$currentNonterminal = $root;
$currentParent = $root;
//$root->printNode();

$currentToken = getNextToken();
$getNextToken = 'getNextToken';


$ast = parser();
startSemanticAnalysis($ast);

function startSemanticAnalysis(object $node)
{
    $arrayOfOperators = array(
        "-", "+", "*", "/", "%", "(", ")"
    );

    $arrayOfLogicOperators = array(
        "&&", "||", "==", "!=", "<", ">", "<=", ">="
    );

    if ($node->typeOfNode === "Variable declaration and initialization") {

        if ($node->dataTypeAndId->dataType === "int") {
            $varType = "int";

            try{
                $expression = $node->expressionOrInitialize->partsOfExpression;
                foreach ($expression as $partOfExpr){
                    if(($partOfExpr["type of data"] !== "numeric_constant") && (!in_array($partOfExpr["data"], $arrayOfOperators))){
                        if($partOfExpr["type of data"] === "id"){
                            $typeOfId = $node->symbolTable->getTypeOfVariable($partOfExpr["data"]);
                            if ($typeOfId !== "int"){
                                throw new Exception("TypeError: unsupported type \"" . $typeOfId . "\" of variable \"" . $partOfExpr["data"] ."\", 'int' expected in line $node->NumOfStringInProgram");
                            }
                        } elseif ($partOfExpr["type of data"] !== "id"){
                            throw new Exception("TypeError: unsupported type \"" . $partOfExpr["type of data"] . "\", 'int' expected in line $node->NumOfStringInProgram");
                        }

                    }
                }
            } catch (Exception $e){
                $errorMessage ="\033[31m" . $e->getMessage();
                print ($errorMessage);
                exit();
            }



            $polish = marshallingYard($node, $node->typeOfNode);
            $node->expressionOrInitialize->partsOfExpression = $polish;
            var_dump($polish);

            //$valueOfId = calculateTheValue($polish);
        } elseif ($node->dataTypeAndId->dataType === "char") {
            $varType = "char";

            try{
                $expression = $node->expressionOrInitialize->partsOfExpression;
                foreach ($expression as $partOfExpr){
                    if($partOfExpr["type of data"] !== "string litheral"){
                        if($partOfExpr["type of data"] === "id"){
                            $typeOfId = $node->symbolTable->getTypeOfVariable($partOfExpr["data"]);
                            if ($typeOfId !== "char"){
                                throw new Exception("TypeError: unsupported type \"" . $typeOfId . "\" of variable \"" . $partOfExpr["data"] ."\", 'char' expected in line $node->NumOfStringInProgram");
                            }
                        } elseif ($partOfExpr["type of data"] !== "id"){
                            throw new Exception("TypeError: unsupported type \"" . $partOfExpr["type of data"] . "\", 'char' expected in line $node->NumOfStringInProgram");
                        }

                    }
                }
            } catch (Exception $e){
                $errorMessage ="\033[31m" . $e->getMessage();
                print ($errorMessage);
                exit();
            }
        }


        $varName = $node->dataTypeAndId->listOfDeclaredVariables[array_key_last($node->dataTypeAndId->listOfDeclaredVariables)];


    } elseif ($node->typeOfNode === "Variable assignment expression") {


        if ($node->variableToAssigning->dataType === "int") {
            $varType = "int";

            try{
                $expression = $node->dataToBeAssigned->partsOfExpression;

                foreach ($expression as $partOfExpr){
                    if(($partOfExpr["type of data"] !== "numeric_constant") && (!in_array($partOfExpr["data"], $arrayOfOperators))){
                        if($partOfExpr["type of data"] === "id"){
                            $typeOfId = $node->symbolTable->getTypeOfVariable($partOfExpr["data"]);
                            if ($typeOfId !== "int"){
                                throw new Exception("TypeError: unsupported type \"" . $partOfExpr["type of data"] . "\" , 'int' expected in line $node->NumOfStringInProgram");
                            }
                        } elseif ($partOfExpr["type of data"] !== "id"){
                            throw new Exception("TypeError: unsupported type \"" . $partOfExpr["type of data"] . "\" , 'int' expected in line $node->NumOfStringInProgram");
                        }

                    }
                }

            } catch (Exception $e){
                $errorMessage ="\033[31m" . $e->getMessage();
                print ($errorMessage);
                exit();
            }


            $polish = marshallingYard($node, $node->typeOfNode);
            $node->dataToBeAssigned->partsOfExpression = $polish;
            //$valueOfId = calculateTheValue($polish);
        } elseif ($node->variableToAssigning->dataType === "char") {
            $varType = "char";

            try{
                $expression = $node->dataToBeAssigned->partsOfExpression;
                foreach ($expression as $partOfExpr){
                    if($partOfExpr["type of data"] !== "string litheral"){
                        if($partOfExpr["type of data"] === "id"){
                            $typeOfId = $node->symbolTable->getTypeOfVariable($partOfExpr["data"]);
                            if ($typeOfId !== "char"){
                                throw new Exception("TypeError: unsupported type \"" . $typeOfId . "\" of variable \"" . $partOfExpr["data"] ."\", 'int' expected in line $node->NumOfStringInProgram");
                            }
                        } elseif ($partOfExpr["type of data"] !== "id"){
                            throw new Exception("TypeError: unsupported type \"" . $partOfExpr["type of data"] . "\", 'int' expected in line $node->NumOfStringInProgram");
                        }

                    }
                }
            } catch (Exception $e){
                $errorMessage ="\033[31m" . $e->getMessage();
                print ($errorMessage);
                exit();
            }
        }


        $varName = $node->variableToAssigning->id;

        //проверяем индекс, если инициализация, проверяем типы инит данных и на переполнение
    } elseif ($node->typeOfNode === "Array declaration" || $node->typeOfNode === "Array declaration and initialization") {
        //print_r($node->dataTypeAndId->sizeOfArray);

        if($node->dataTypeAndId->typeOfArraySizeValue !== "numeric_constant"){
            if($node->dataTypeAndId->typeOfArraySizeValue === "id"){
                $typeOfId = $node->symbolTable->getTypeOfVariable($node->dataTypeAndId->sizeOfArray);
                if ($typeOfId !== "int"){
                    throw new Exception("TypeError: unsupported type \"" . $typeOfId . "\" , 'int' expected in line $node->NumOfStringInProgram");
                }
            } elseif ($node->dataTypeAndId->typeOfArraySizeValue !== "id"){
                throw new Exception("TypeError: unsupported type \"" . $node->dataTypeAndId->typeOfArraySizeValue . "\" , 'int' expected in line $node->NumOfStringInProgram");
            }

        }

        if($node->typeOfNode === "Array declaration and initialization"){
            $elementsOfArr = $node->expressionOrInitialize->partsOfExpression;
            foreach ($elementsOfArr as $partOfArr){
                if(($partOfArr["type of data"] !== "numeric_constant") && ($partOfArr["type of data"] !== "comma")){
                    if($partOfArr["type of data"] === "id"){
                        $typeOfId = $node->symbolTable->getTypeOfVariable($partOfArr["data"]);
                        if ($typeOfId !== "int"){
                            throw new Exception("TypeError: unsupported type \"" . $partOfArr["type of data"] . "\" , 'int' expected in line $node->NumOfStringInProgram");
                        }
                    } elseif ($partOfArr["type of data"] !== "id"){
                        throw new Exception("TypeError: unsupported type \"" . $partOfArr["type of data"] . "\" , 'int' expected in line $node->NumOfStringInProgram");
                    }

                }
            }
        }

    } elseif ($node->typeOfNode === "while loop" || $node->typeOfNode === "conditional jump operator if"){
        //print_r($node->loopCondition->partsOfExpression);
        if($node->typeOfNode === "while loop"){
            $countOfParts = count($node->loopCondition->partsOfExpression);
            $expression = $node->loopCondition->partsOfExpression;
        } else{
            $countOfParts = count($node->ifSTMTCondition->partsOfExpression);
            $expression = $node->ifSTMTCondition->partsOfExpression;
        }

        $checkTypeOfFirst = $expression[0];
        if($checkTypeOfFirst["type of data"] === "id"){
            $typeOfId = $node->symbolTable->getTypeOfVariable($checkTypeOfFirst["data"]);
            if ($typeOfId === "char" || $typeOfId === "int"){
                $typeOfFirst = $typeOfId;
            }
        } elseif ($checkTypeOfFirst["type of data"] === "string litheral"){
            $typeOfFirst = "char";
        } elseif ($checkTypeOfFirst["type of data"] === "numeric_constant"){
            $typeOfFirst = "int";
        }
        //выражение с чар
        if (($countOfParts === 1 || $countOfParts === 3) && ($typeOfFirst === "char")){
            $varType = "char";

            try{

                foreach ($expression as $partOfExpr){
                    if(($partOfExpr["type of data"] !== "string litheral") && (!in_array($partOfExpr["data"], $arrayOfLogicOperators))){
                        if($partOfExpr["type of data"] === "id"){
                            $typeOfId = $node->symbolTable->getTypeOfVariable($partOfExpr["data"]);
                            if ($typeOfId !== "char"){
                                throw new Exception("TypeError: unsupported type \"" . $typeOfId . "\" of variable \"" . $partOfExpr["data"] ."\", 'char' expected in line $node->NumOfStringInProgram");
                            }
                        } elseif ($partOfExpr["type of data"] !== "id"){
                            throw new Exception("TypeError: unsupported type \"" . $partOfExpr["type of data"] . "\", 'char' expected in line $node->NumOfStringInProgram");
                        }

                    }
                }
            } catch (Exception $e){
                $errorMessage ="\033[31m" . $e->getMessage();
                print ($errorMessage);
                exit();
            }
        } else{ //выражение с инт
            $varType = "int";

            try{

                foreach ($expression as $partOfExpr){
                    if(($partOfExpr["type of data"] !== "numeric_constant") && (!in_array($partOfExpr["data"], $arrayOfOperators)) && (!in_array($partOfExpr["data"], $arrayOfLogicOperators))){
                        if($partOfExpr["type of data"] === "id"){
                            $typeOfId = $node->symbolTable->getTypeOfVariable($partOfExpr["data"]);
                            if ($typeOfId !== "int"){
                                throw new Exception("TypeError: unsupported type \"" . $partOfExpr["type of data"] . "\" , 'int' expected in line $node->NumOfStringInProgram");
                            }
                        } elseif ($partOfExpr["type of data"] !== "id"){
                            throw new Exception("TypeError: unsupported type \"" . $partOfExpr["type of data"] . "\" , 'int' expected in line $node->NumOfStringInProgram");
                        }

                    }
                }

            } catch (Exception $e){
                $errorMessage ="\033[31m" . $e->getMessage();
                print ($errorMessage);
                exit();
            }

        }


    }


    if (isset($node->childNode)) {
        startSemanticAnalysis($node->childNode);
    }

    if (isset($node->nextNode)) {
        startSemanticAnalysis($node->nextNode);
    }

}


function marshallingYard($exprNode, $typeOfExpr)
{
    global $syntaxErrorHandler;
    $operatorsStack = [];
    $subTable = $exprNode->symbolTable;
    $outputArr = [];

    $arrayOfOperators = array(
        "-" => 1, "+" => 1, "*" => 2, "/" => 2, "%" => 2
    );

    $arrayOfLogicOperators = array(
        "&&", "||", "!", "==", "!=", "<", ">", "<=", ">="
    );

    if( $exprNode->typeOfNode === "Variable assignment expression"){
        $expression = $exprNode->dataToBeAssigned->partsOfExpression;
    } elseif($exprNode->typeOfNode === "Variable declaration and initialization") {
        $dataTypeOfExpr = $exprNode->expressionOrInitialize->typeOfExpression;
        $expression = $exprNode->expressionOrInitialize->partsOfExpression;
    } else{
        print("semantic analysis is failed");
        exit();
    }



    if(!is_array($expression)){
        echo "bruh";
        exit();
    }

    foreach ($expression as $part) {

        if ($part["type of data"] === "numeric_constant") {
            //кидаем в выходной массив
            $outputArr[] = $part["data"];

        } elseif ($part["type of data"] === "LParen") {

            $operatorsStack[] = $part["data"];
        } elseif ($part["type of data"] === "RParen") {

            while ($operatorsStack[array_key_last($operatorsStack)] !== "(") {
                $lastOperator = array_pop($operatorsStack);

                $outputArr[] = $lastOperator;
            }

            //последний элемент должен быть открывающей скобкой - убираем
            array_pop($operatorsStack);

        } elseif ($part["type of data"] === "id") {

            $idType = $exprNode->symbolTable->getTypeOfVariable($part["data"]);
            if ($idType !== "int"){

                throw new Exception("TypeError: unsupported operand type of the variable '" . $part["data"] . "' in line $exprNode->NumOfStringInProgram ");
            }

            /*
            $idValue = $exprNode->symbolTable->getValueOfVariable($part["data"]);

            if (!$idValue){
                throw new Exception("the variable " . $part["data"] . " doesn't have value");
            } */
            $outputArr[] = $part["data"];

        } elseif (array_key_exists($part["data"], $arrayOfOperators)) {
            $lastOperator = $operatorsStack[array_key_last($operatorsStack)];

            $inputOperator = $part["data"];

            if ((count($operatorsStack) > 0) && ($arrayOfOperators["$lastOperator"] >= $arrayOfOperators["$inputOperator"])) {
                $lastOperator = array_pop($operatorsStack);
                $outputArr[] = $lastOperator;
                $operatorsStack[] = $inputOperator;
            } else {
                $operatorsStack[] = $inputOperator;
            }

        }
    }


    while (count($operatorsStack)) {
        $lastOperator = array_pop($operatorsStack);
        $outputArr[] = $lastOperator;
    }

    return $outputArr;
}

function calculateTheValue($arrayRPN){
    $arrayOfOperators = array(
        "-", "+", "*", "/", "%",
    );

    $calcArray = [];


        for ($i = 0; $i < count($arrayRPN); $i++){
            if(ctype_digit($arrayRPN[$i])){
                $calcArray[] = $arrayRPN[$i];
            } elseif (in_array($arrayRPN[$i], $arrayOfOperators)){
                $secondValue = array_pop($calcArray);
                $firstValue = array_pop($calcArray);
                switch ($arrayRPN[$i]){
                    case '-':
                        $resultValue = subtractTwoValues($firstValue, $secondValue);
                        array_push($calcArray, $resultValue);
                        break;
                    case '+':
                        $resultValue = addTwoValues($firstValue, $secondValue);
                        array_push($calcArray, $resultValue);
                        break;
                    case '*':
                        $resultValue = multiplyTwoValues($firstValue, $secondValue);
                        array_push($calcArray, $resultValue);
                        break;
                    case '/':
                        $resultValue = divideTwoValues($firstValue, $secondValue);
                        array_push($calcArray, $resultValue);
                        break;
                    case '%':
                        $resultValue = findModuloOfTwoValues($firstValue, $secondValue);
                        array_push($calcArray, $resultValue);
                        break;
                }
            }
        }

    return $calcArray[0];
}

function addTwoValues($first, $second)
{
    if (ctype_digit($first)){
        if (ctype_digit($second)){
            return (int)$first + (int)$second;
        } else{
            throw new Exception("TypeError: unsupported operand type of '$second' for '+' ");
        }
    } else{
        throw new Exception("TypeError: unsupported operand type of '$first' for '+' ");
    }
}

function multiplyTwoValues($first, $second)
{
    if (ctype_digit($first)){
        if (ctype_digit($second)){
            return (int)$first * (int)$second;
        } else{
            throw new Exception("TypeError: unsupported operand type '$second' for '*' ");
        }
    } else{
        throw new Exception("TypeError: unsupported operand type '$first' for '*' ");
    }
}

function subtractTwoValues($first, $second)
{
    if (ctype_digit($first)){
        if (ctype_digit($second)){
            return (int)$first - (int)$second;
        } else{
            throw new Exception("TypeError: unsupported operand type '$second' for '-' ");
        }
    } else{
        throw new Exception("TypeError: unsupported operand type '$first' for '-' ");
    }
}

function divideTwoValues($first, $second)
{
    if (ctype_digit($first)){
        if (ctype_digit($second)){
            return (int)$first / (int)$second;
        } else{
            throw new Exception("TypeError: unsupported operand type '$second' for '/' ");
        }
    } else{
        throw new Exception("TypeError: unsupported operand type '$first' for '/' ");
    }
}

function findModuloOfTwoValues($first, $second){
    if (ctype_digit($first)){
        if (ctype_digit($second)){
            $result = $first % $second;
            return (int) $result;
        } else{
            throw new Exception("TypeError: unsupported operand type '$second' for '%' ");
        }
    } else{
        throw new Exception("TypeError: unsupported operand type '$first' for '%' ");
    }

}