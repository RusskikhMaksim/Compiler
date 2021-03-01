<?php

/*
TO DO : объявление сразу нескольких переменных через запятую done => объявление через запятую с инициализацией => протестировать
        разобраться с массивами  - done
        парсинг инкремента выражения -
        елс иф / елс - done
        исправить подсчёт уровня во время уменьшения уровня вложенности - done
        вывод принтефа
        написать тесты для функций
        реализовать вывод по флагам
        провести рефакторинг названия классов - done
        рефакторинг : разложить функцию объявления, уменьшить вложенность
        рефакторинг : вынести иссет с родителем и ребёнком в отдельную функцию
        рефакторинг : параметризировать аргументы функций парсинга
        рефакторинг : вместо пересылки объекта, обработки и возвращения ЕГО ЖЕ, пересылать одно, внутри функции создавать и работать с другим и
        возвращать другое


 */
//declare(strict_types=1);
//set_include_path("D:\OPENSERVER\OpenServer\domains\compile\src");
require_once './include.php';
//основной модуль выполнения парсинга
//пока остаются токены, сканируем и вызываем соответствующую функцию парсинга
//основной модуль выполнения парсинга
//пока остаются токены, сканируем и вызываем соответствующую функцию парсинга
//объявление либо объявление переменной с инициализацией
function parser(){
    global $currentToken;
    global $currentNonterminal;
    global $currentTable;
    global $subTable;
    global $tokenArrayIndex;
    global $Token;
    global $currentParent;
    global $nestingLevelCounter;
    global $ast;

    while ($tokenArrayIndex <= count($Token)) {

        if ($currentToken->tokenClass === "KeyWord #include") {
            $currentNonterminal = preprocessorDirectiveNodeFunc($currentParent, $currentToken, $nestingLevelCounter);
        }




        if ($currentToken->tokenClass === "datatype") {

            $currentNonterminal = declareSomething($currentNonterminal, $currentParent, $currentToken, $nestingLevelCounter);

            if ($currentNonterminal->typeOfNode === "Function declaration") {
                $currentParent = $currentNonterminal;



            } elseif ($currentNonterminal->nestingLevel === $currentParent->nestingLevel) {
                $currentParent = $currentParent->parentNode;
            }

            if ($currentNonterminal->typeOfNode !== "Function declaration"){
                $subTable->setVariable($subTable, $currentNonterminal);
            }

            //вызов обработки инициализации
            if ($currentNonterminal->dataTypeAndId->declareWithInitialize === TRUE) {
                $currentNonterminal = expressionNode($currentNonterminal, $currentToken, $nestingLevelCounter);
               // var_dump($currentNonterminal->expressionOrInitialize);
            }
            //$currentNonterminal->printNode();
            $currentNonterminal->symbolTable = $subTable;

        }


        //вызов функции либо присваивание переменной
        if ($currentToken->tokenClass === "id") {
            //проверить переменные на использование в вводе и выводе
            if ($currentToken->bodyOfToken === "printf" || $currentToken->bodyOfToken === "scanf") {
                $currentNonterminal = inputOrOutputNode($currentNonterminal, $currentParent, $currentToken, $nestingLevelCounter);
                //$currentNonterminal->printNode();

            } else {
                //var_dump($subTable);
                $existsOrNot = $subTable->checkIfExists("", $currentToken->bodyOfToken);
                //ump($existsOrNot);
                if(!$existsOrNot){
                    $errorMessage = "\033[31m" . $currentToken->bodyOfToken . " without definition in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }
                //проверить объявлена ли переменная
                $currentNonterminal = assigmentNode($currentNonterminal, $currentParent, $currentToken, $nestingLevelCounter);
                //$currentNonterminal->printNode();
                //print_r($currentNonterminal->dataToBeAssigned->partsOfExpression);
            }
            if ($currentNonterminal->nestingLevel === $currentParent->nestingLevel) {
                $currentParent = $currentParent->parentNode;
            }
            if ($currentNonterminal->dataTypeAndId->declareWithInitialize === TRUE) {
                expressionNode($currentNonterminal, $currentToken, $nestingLevelCounter);
               // var_dump($currentNonterminal->expressionOrInitialize);
            }
        }

        if ($currentToken->tokenClass === "KeyWord if" || $currentToken->tokenClass === "KeyWord else if" || $currentToken->tokenClass === "KeyWord else") {

            $subTable = new SymbolTableClass($currentTable);

            $currentNonterminal = ifStatementNode($currentNonterminal, $currentParent, $currentToken, $nestingLevelCounter);
            $currentParent = $currentNonterminal;


        }

        if ($currentToken->tokenClass === "KeyWord while") {

            $subTable = new SymbolTableClass($currentTable);

            $currentNonterminal = whileLoopNode($currentNonterminal, $currentParent, $currentToken, $nestingLevelCounter);
            $currentParent = $currentNonterminal;



            //вызов функции по парсингу конструкции while
            //сначала парсим выражение указанное в словии
            //затем тело цикла
        }

        if ($currentToken->tokenClass === "KeyWord return") {
            $currentNonterminal = keyWordReturnNode($currentNonterminal, $currentParent, $currentToken, $nestingLevelCounter);
            //$currentNonterminal->printNode();
            if ($currentNonterminal->nestingLevel === $currentParent->nestingLevel) {
                $currentParent = $currentParent->parentNode;
            }

        }
        $currentToken = getNextToken();
    }

    //printAST($ast);
    return $ast;
}



function preprocessorDirectiveNodeFunc(object $currentParent, object $currentToken, $nestingLevelCounter): object
{
    global $nestingLevelCounter;
    $keyWordIncludeNode = new AstPreprocessorDirectiveClass($nestingLevelCounter);
    $keyWordIncludeNode->typeOfNode = "Preprocessor directive include";
    $keyWordIncludeNode->directive = $currentToken->bodyOfToken;
    $keyWordIncludeNode->NumOfStringInProgram = $currentToken->NumOfStringInProgram;
    $calleeLib = "";


    for ($i = 0; $i < 5; $i++) {
        $currentToken = getNextToken();
        $calleeLib .= $currentToken->bodyOfToken;
    }
    $keyWordIncludeNode->calleeLib = $calleeLib;
    $currentParent->childNode = $keyWordIncludeNode;
    $keyWordIncludeNode->parentNode = $currentParent;

    return $keyWordIncludeNode;
}

function declareSomething(object $previousNonterminal, object $currentParent, object $currentToken, $nestingLevelCounter): object
{
    global $syntaxErrorHandler;
    global $tokenArrayIndex;
    global $subTable;
    global $currentTable;

    $declarationNode = new AstDeclarationClass($nestingLevelCounter);
    $declarationNode->NumOfStringInProgram = $currentToken->NumOfStringInProgram;
    $datatypeOfNonterminal = $currentToken->bodyOfToken;
    $currentToken = getNextToken();
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

    $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
    $syntaxErrorHandler->checkForSyntaxErrors("declaration", $currentToken);
    $currentToken = getNextToken();

    //объявление функции
    if ($currentToken->bodyOfToken === "(") {

        $subTable = new SymbolTableClass($currentTable);

        $declarationNode->typeOfNode = "Function declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and function name";

        while ($currentToken->bodyOfToken !== ")") {
            $currentToken = getNextToken();
        }

        $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
        $syntaxErrorHandler->checkForSyntaxErrors("function declaration", $currentToken);
        $currentToken = getNextToken();
        if ($currentToken->bodyOfToken === "{") {
            return $declarationNode;
        } else {
            printf("missed \"{\" on pos %d in str %d", $currentToken->startPositionInString, $currentToken->NumOfStringInProgram);
            return $declarationNode;
        }
    }


    //объявление переменной
    if ($currentToken->bodyOfToken === ";") {
        //проверяем, была ли уже объявлена эта переменная
        try {
            $subTable->checkIfExists($declarationNode->dataTypeAndId->dataType, $declarationNode->dataTypeAndId->id);
        } catch (RedifinationException $e){
            $errorMessage = "\033[31m" . $e->getMessage() . " in line " . $currentToken->NumOfStringInProgram;
            print ($errorMessage);
            exit();
        }
        //добавляем в таблицу
        $declarationNode->typeOfNode = "Variable declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of variable";
        $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $declarationNode->dataTypeAndId->id;

        return $declarationNode;

    } elseif ($currentToken->bodyOfToken === ",") {
        $declarationNode->typeOfNode = "Variables declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of variable";
        $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $declarationNode->dataTypeAndId->id;


        while ($currentToken->bodyOfToken !== ";") {
            if ($currentToken->bodyOfToken === "=") {
                //проверяем, были ли уже объявлены эти переменные
                foreach ($declarationNode->dataTypeAndId->listOfDeclaredVariables as $id){
                    if ($id !== ","){
                        try {
                            $subTable->checkIfExists($declarationNode->dataTypeAndId->dataType, $id);
                        } catch (RedifinationException $e){
                            $errorMessage = "\033[31m" . $e->getMessage() . " in line " . $currentToken->NumOfStringInProgram;
                            print ($errorMessage);
                            exit();
                        }
                    }
                }

                $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
                $declarationNode->typeOfNode = "Variables declaration and initialization";
                return $declarationNode;
            }
            if ($currentToken->bodyOfToken === "[") {
                $arrayLexeme = "";
                for ($i = 0; $i < 3; $i++) {
                    $arrayLexeme .= $currentToken->bodyOfToken;
                    $currentToken = getNextToken();
                }
                $declarationNode->isArray = $declarationNode->dataTypeAndId->listOfDeclaredVariables[array_key_last($declarationNode->dataTypeAndId->listOfDeclaredVariables)];
                $declarationNode->dataTypeAndId->listOfDeclaredVariables[array_key_last($declarationNode->dataTypeAndId->listOfDeclaredVariables)] .= $arrayLexeme;
                continue;
            }
            $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $currentToken->bodyOfToken;
            $currentToken = getNextToken();
        }

        //проверяем, были ли уже объявлены эти переменные
        foreach ($declarationNode->dataTypeAndId->listOfDeclaredVariables as $id){
            if ($id !== ","){
                try {
                    $subTable->checkIfExists($declarationNode->dataTypeAndId->dataType, $id);
                } catch (RedifinationException $e){
                    $errorMessage = "\033[31m" . $e->getMessage() . " in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }
            }
        }
        return $declarationNode;
    }

    //объявление массива

    if ($currentToken->bodyOfToken === "[") {
        //проверяем, был ли уже объявлен этот массив
        try {
            $subTable->checkIfExists($declarationNode->dataTypeAndId->dataType, $declarationNode->dataTypeAndId->id);
        } catch (RedifinationException $e){
            $errorMessage = "\033[31m" . $e->getMessage() . " in line " . $currentToken->NumOfStringInProgram;
            print ($errorMessage);
            exit();
        }

        $arrayLexeme = $declarationNode->dataTypeAndId->id;
        $declarationNode->isArray = $declarationNode->dataTypeAndId->id;
        $declarationNode->typeOfNode = "Array declaration";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of array";
        $arrayLexeme .= $currentToken->bodyOfToken;
        $currentToken = getNextToken();

        if( $currentToken->tokenClass === "id"){

            $existsOrNot = $subTable->checkIfExists("", $currentToken->bodyOfToken);
            //ump($existsOrNot);
            if(!$existsOrNot){
                $errorMessage = "\033[31m" . $currentToken->bodyOfToken . " without definition in line " . $currentToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            }
        }

        $arrayLexeme .= $currentToken->bodyOfToken;
        //если полученный токен - число либо переменная, то указывается размерность массива
        if ($currentToken->tokenClass === "numeric_constant" || $currentToken->tokenClass === "id" || $currentToken->tokenClass !== "r_sqparen") {
            $declarationNode->dataTypeAndId->sizeOfArray = $currentToken->bodyOfToken;
            $declarationNode->dataTypeAndId->typeOfArraySizeValue = $currentToken->tokenClass;
            $currentToken = getNextToken();
            $arrayLexeme .= $currentToken->bodyOfToken;
            if ($currentToken->tokenClass !== "r_sqparen") {
                //пропущена закрывающая квадратная скобка
                printf("missed \"]\" on pos %d in str %d", $currentToken->startPositionInString, $currentToken->NumOfStringInProgram);
            }
            $currentToken = getNextToken();
            //только объявление массива
            try {
                $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
                $syntaxErrorHandler->checkIfSemicolonMissed($currentToken->bodyOfToken);
            } catch (MissedLexemeException $e){
                if($currentToken->bodyOfToken !== "="){
                    $errorMessage = "\033[31m Missed semicolon " .  "in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }

            }
            if ($currentToken->bodyOfToken === ";") {
                $declarationNode->dataTypeAndId->declareWithInitialize = FALSE;
                $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $arrayLexeme;
                return $declarationNode;
            } //инициализация, обрабатывается далее отдельно
            elseif ($currentToken->bodyOfToken === "=") {
                $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
                $declarationNode->typeOfNode = "Array declaration and initialization";
                $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $arrayLexeme;
                return $declarationNode;
            } elseif ($currentToken->bodyOfToken === ",") {
                $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $arrayLexeme;


                while ($currentToken->bodyOfToken !== ";") {
                    if ($currentToken->bodyOfToken === "=") {
                        //проверяем, были ли уже объявлены эти переменные
                        foreach ($declarationNode->dataTypeAndId->listOfDeclaredVariables as $id){
                            if ($id !== ","){
                                try {
                                    $subTable->checkIfExists($declarationNode->dataTypeAndId->dataType, $id);
                                } catch (RedifinationException $e){
                                    $errorMessage = "\033[31m" . $e->getMessage() . " in line " . $currentToken->NumOfStringInProgram;
                                    print ($errorMessage);
                                    exit();
                                }
                            }
                        }
                        $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
                        $declarationNode->typeOfNode = "Variables declaration and initialization";
                        return $declarationNode;
                    }
                    if ($currentToken->bodyOfToken === "[") {
                        $arrayLexeme = "";
                        for ($i = 0; $i < 3; $i++) {
                            $arrayLexeme .= $currentToken->bodyOfToken;
                            $currentToken = getNextToken();
                        }
                        $declarationNode->dataTypeAndId->listOfDeclaredVariables[array_key_last($declarationNode->dataTypeAndId->listOfDeclaredVariables)] .= $arrayLexeme;
                        continue;
                    }
                    $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $currentToken->bodyOfToken;
                    $currentToken = getNextToken();
                }

                //проверяем, были ли уже объявлены эти переменные
                foreach ($declarationNode->dataTypeAndId->listOfDeclaredVariables as $id){
                    if ($id !== ","){
                        try {
                            $subTable->checkIfExists($declarationNode->dataTypeAndId->dataType, $id);
                        } catch (RedifinationException $e){
                            $errorMessage = "\033[31m" . $e->getMessage() . " in line " . $currentToken->NumOfStringInProgram;
                            print ($errorMessage);
                            exit();
                        }
                    }
                }

                return $declarationNode;
            }

        } //если кв. скобка, значит размерность опускается, далее будет инициализация
        elseif ($currentToken->tokenClass === "r_sqparen") {
            $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
            $declarationNode->typeOfNode = "Array declaration and initialization";
            $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $declarationNode->dataTypeAndId->id . "[]";
            return $declarationNode;

        }
    } //объявление переменной и её инициализация
    elseif ($currentToken->bodyOfToken === "=") {
        //проверяем, были ли уже объявлены эти переменные
        try {
            $subTable->checkIfExists($declarationNode->dataTypeAndId->dataType, $declarationNode->dataTypeAndId->id);
        } catch (RedifinationException $e){
            $errorMessage = "\033[31m" . $e->getMessage() . " in line " . $currentToken->NumOfStringInProgram;
            print ($errorMessage);
            exit();
        }
        $declarationNode->typeOfNode = "Variable declaration and initialization";
        $declarationNode->dataTypeAndId->typeOfNode = "Data type and name of variable";
        $declarationNode->dataTypeAndId->declareWithInitialize = TRUE;
        $declarationNode->dataTypeAndId->listOfDeclaredVariables[] = $declarationNode->dataTypeAndId->id;
        return $declarationNode;
        //функция парсинга выражений

    }
}

function whileLoopNode($previousNonterminal, $currentParent, $currentToken, $nestingLevelCounter)
{
    global $syntaxErrorHandler;
    global $tokenArrayIndex;
    global $subTable;
    global $currentTable;

    $whileLoopNode = new AstWhileClass($nestingLevelCounter);

    $whileLoopNode->typeOfNode = "while loop";
    $whileLoopNode->parentNode = $currentParent;
    $whileLoopNode->NumOfStringInProgram = $currentToken->NumOfStringInProgram;
    $whileLoopNode->symbolTable = $subTable;

    if (isset($currentParent->childNode) && ($whileLoopNode->nestingLevel > $currentParent->nestingLevel)) {
        $previousNonterminal->nextNode = $whileLoopNode;
        $whileLoopNode->parentNode = $currentParent;
    } elseif (isset($currentParent->childNode) && ($whileLoopNode->nestingLevel < $currentParent->nestingLevel)) {
        while ($whileLoopNode->nestingLevel < $currentParent->nestingLevel) {
            $currentParent = $currentParent->parentNode;
        }
        $currentParent->nextNode = $whileLoopNode;
        $whileLoopNode->parentNode = $currentParent->parentNode;
    } elseif (isset($currentParent->childNode) && $whileLoopNode->nestingLevel === $currentParent->nestingLevel) {
        $currentParent->nextNode = $whileLoopNode;
        $whileLoopNode->parentNode = $currentParent->parentNode;
    } else {
        $currentParent->childNode = $whileLoopNode;
        $whileLoopNode->parentNode = $currentParent;
    }

    //$whileLoopNode

    $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
    $syntaxErrorHandler->checkForSyntaxErrors("while", $currentToken);
    //$currentLexeme = $currentToken->bodyOfToken;
    //syntaxErrorHandler("while", $currentLexeme);
    $currentToken = getNextToken();

    $whileLoopNode = expressionNode($whileLoopNode, $currentToken, $nestingLevelCounter);
    //var_dump($whileLoopNode->expressionOrInitialize);


    return $whileLoopNode;
}

function syntaxErrorHandler(string $typeOfNode, $previousToken) {
    //принимаем тип узла и запускаем соответствующую проверку
    //если ошибка, бросам исключение, ловим его, выключаем программу.
    if( $typeOfNode === "while") {
        $expectedToken = "(";
        $actualToken = checkNextToken();
        $actualLexeme = $actualToken->bodyOfToken;
    } elseif ($typeOfNode === "expression"){

    } elseif ($typeOfNode === "declaration"){

    }


    if(isset($expectedToken) && isset($actualLexeme)) {
        try {
            checkSyntax($previousToken, $expectedToken, $actualLexeme);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage() . "on position " . $actualToken->startPositionInString . " in line " . $actualToken->NumOfStringInProgram;
            print ($errorMessage);
            exit();
        }
    }

}

function checkSyntax($previousToken ,$expectedToken, $actualToken){
    if($expectedToken !== $actualToken){
        throw new Exception("Expected \"$expectedToken\" after \"$previousToken\", got \"$actualToken\" instead\n");
    }
}

function ifStatementNode($previousNonterminal, $currentParent, $currentToken, $nestingLevelCounter)
{
    $ifStatementNode = new AstIfClass($nestingLevelCounter);
    global $syntaxErrorHandler;
    global $tokenArrayIndex;
    global $subTable;
    global $currentTable;
    $ifStatementNode->symbolTable = $subTable;
    $ifStatementNode->NumOfStringInProgram = $currentToken->NumOfStringInProgram;

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

        $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
        $syntaxErrorHandler->checkForSyntaxErrors("if", $currentToken);
        $currentToken = getNextToken();

        if ($currentToken->bodyOfToken === "(") {
            $ifStatementNode = expressionNode($ifStatementNode, $currentToken, $nestingLevelCounter);

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

function expressionNode($previousNonterminal, $currentToken, $nestingLevelCounter): object
{
    global $syntaxErrorHandler;
    global $tokenArrayIndex;
    global $subTable;
    global $currentTable;

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
            $currentToken = getNextToken();
        }


        $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
        $syntaxErrorHandler->checkForSyntaxErrors("array initialization", $currentToken);
        $currentToken = getNextToken();


        if ($currentToken->bodyOfToken === "{") {
            $currentToken = getNextToken();
            while ($currentToken->bodyOfToken !== "}") {
                //if($currentToken->tokenClass === "id" || $currentToken->tokenClass === $expectedTypeOfInputData) {

                if( $currentToken->tokenClass === "id"){

                    try {
                        $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
                        $syntaxErrorHandler->validateIdName($currentToken->bodyOfToken);
                    } catch (Exception $e){
                        $errorMessage ="\033[31m" . $e->getMessage() . "on position " . $currentToken->startPositionInString . " in line " . $currentToken->NumOfStringInProgram;
                        print ($errorMessage);
                        exit();
                    }

                    $existsOrNot = $subTable->checkIfExists("", $currentToken->bodyOfToken);
                    $arrayOrNot = $subTable->checkIfArray($currentToken->bodyOfToken);
                    //ump($existsOrNot);
                    if(!$existsOrNot){
                        $errorMessage = "\033[31m" . $currentToken->bodyOfToken . " without definition in line " . $currentToken->NumOfStringInProgram;
                        print ($errorMessage);
                        exit();
                    } elseif ($arrayOrNot){
                        $errorMessage = "\033[31m" . "Syntax error: " . $currentToken->bodyOfToken . " is array, in line " . $currentToken->NumOfStringInProgram;
                        print ($errorMessage);
                        exit();
                    }
                }

                $partsOfExpr[] = array(
                    "type of data" => "$currentToken->tokenClass",
                    "data" => $currentToken->bodyOfToken
                );
                //}
                //if($currentToken->bodyOfToken === ","){
                //    $currentToken = getNextToken();
                //    continue;
                //}
                $currentToken = getNextToken();
            }

            $previousNonterminal->expressionOrInitialize->partsOfExpression = $partsOfExpr;
            return $previousNonterminal;
        }


    } //инициализация переменной
    elseif ($previousNonterminal->typeOfNode === "Variable declaration and initialization" || $previousNonterminal->typeOfNode === "Variables declaration and initialization") {
        $previousNonterminal->expressionOrInitialize->typeOfExpression = "Initialization of id";
        $currentToken = getNextToken();


        if( $currentToken->tokenClass === "id" || $currentToken->tokenClass === "unknown"){

            try {
                $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
                $syntaxErrorHandler->validateIdName($currentToken->bodyOfToken);
            } catch (Exception $e){
                $errorMessage ="\033[31m" . $e->getMessage() . "on position " . $currentToken->startPositionInString . " in line " . $currentToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            }


            $existsOrNot = $subTable->checkIfExists("", $currentToken->bodyOfToken);
            $arrayOrNot = $subTable->checkIfArray($currentToken->bodyOfToken);
            //ump($existsOrNot);
            if(!$existsOrNot){
                $errorMessage = "\033[31m" . $currentToken->bodyOfToken . " without definition in line " . $currentToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            } elseif ($arrayOrNot){
                $errorMessage = "\033[31m" . "Syntax error: " . $currentToken->bodyOfToken . " is array, in line " . $currentToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            }
        }

        //print_r($currentToken);
        //если переменной присваивается char символ

        //если числовое выражение
        //elseif($currentToken->tokenClass === "int"){
        $strNum = $currentToken->NumOfStringInProgram;
        $isExprClosed = false;
        //считываем выражение
        if ($currentToken->tokenClass === "string litheral") {
            //if($currentToken->tokenClass === "string litheral"){
            $partsOfExpr[] = array(
                "type of data" => "$currentToken->tokenClass",
                "data" => $currentToken->bodyOfToken
            );
            //}
            //копируем собранное выражение в узел
            $previousNonterminal->expressionOrInitialize->partsOfExpression = $partsOfExpr;
            return $previousNonterminal;
        }

        while ($currentToken->bodyOfToken !== ";" && $currentToken->NumOfStringInProgram === $strNum) {

            if( $currentToken->tokenClass === "id"){

                try {
                    $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
                    $syntaxErrorHandler->validateIdName($currentToken->bodyOfToken);
                } catch (Exception $e){
                    $errorMessage ="\033[31m" . $e->getMessage() . "on position " . $currentToken->startPositionInString . " in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }

                $existsOrNot = $subTable->checkIfExists("", $currentToken->bodyOfToken);
                $arrayOrNot = $subTable->checkIfArray($currentToken->bodyOfToken);
                //ump($existsOrNot);
                if(!$existsOrNot){
                    $errorMessage = "\033[31m" . $currentToken->bodyOfToken . " without definition in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                } elseif ($arrayOrNot){
                    $errorMessage = "\033[31m" . "Syntax error: " . $currentToken->bodyOfToken . " is array, in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }
            }

            $partsOfExpr[] = array(
                "type of data" => "$currentToken->tokenClass",
                "data" => "$currentToken->bodyOfToken"
            );

            $currentToken = getNextToken();
        }
        //копируем собранное выражение в узел
        $previousNonterminal->expressionOrInitialize->partsOfExpression = $partsOfExpr;
        return $previousNonterminal;


    } //выражение в качестве части операции присваивания
    elseif ($previousNonterminal->typeOfNode === "Variable assignment expression") {
        $previousNonterminal->dataToBeAssigned->typeOfExpression = "Variable assignment expression";
        $currentToken = getNextToken();
        $endOfExpr = ";";

        if( $currentToken->tokenClass === "id"){
            $existsOrNot = $subTable->checkIfExists("", $currentToken->bodyOfToken);
            $arrayOrNot = $subTable->checkIfArray($currentToken->bodyOfToken);
            //ump($existsOrNot);
            if(!$existsOrNot){
                $errorMessage = "\033[31m" . $currentToken->bodyOfToken . " without definition in line " . $currentToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            } elseif ($arrayOrNot){
                $errorMessage = "\033[31m" . "Syntax error: " . $currentToken->bodyOfToken . " is array, in line " . $currentToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            }
        }

        //если числовое выражение
        $strNum = $currentToken->NumOfStringInProgram;
        $isExprClosed = false;
        //считываем выражение
        while ($currentToken->bodyOfToken !== $endOfExpr && $currentToken->NumOfStringInProgram === $strNum) {

            if( $currentToken->tokenClass === "id"){
                $existsOrNot = $subTable->checkIfExists("", $currentToken->bodyOfToken);
                $arrayOrNot = $subTable->checkIfArray($currentToken->bodyOfToken);
                //ump($existsOrNot);
                if(!$existsOrNot){
                    $errorMessage = "\033[31m" . $currentToken->bodyOfToken . " without definition in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                } elseif ($arrayOrNot){
                    $errorMessage = "\033[31m" . "Syntax error: " . $currentToken->bodyOfToken . " is array, in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }
            }

            $partsOfExpr[] = array(
                "type of data" => "$currentToken->tokenClass",
                "data" => "$currentToken->bodyOfToken"
            );
            $currentToken = getNextToken();
        }
        //копируем собранное выражение в узел
        $previousNonterminal->dataToBeAssigned->partsOfExpression = $partsOfExpr;
        return $previousNonterminal;

    } elseif ($previousNonterminal->typeOfNode === "while loop") {
        $previousNonterminal->loopCondition->typeOfExpression = "loop condition expression";

        $currentToken = getNextToken();
        $endOfExpr = "{";
        $strNum = $currentToken->NumOfStringInProgram;
        $isExprClosed = false;

        while ($currentToken->bodyOfToken !== $endOfExpr && $currentToken->NumOfStringInProgram === $strNum) {

            if( $currentToken->tokenClass === "id"){
                $existsOrNot = $subTable->checkIfExists("", $currentToken->bodyOfToken);
                $arrayOrNot = $subTable->checkIfArray($currentToken->bodyOfToken);
                //ump($existsOrNot);
                if(!$existsOrNot){
                    $errorMessage = "\033[31m" . $currentToken->bodyOfToken . " without definition in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                } elseif ($arrayOrNot){
                    $errorMessage = "\033[31m" . "Syntax error: " . $currentToken->bodyOfToken . " is array, in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }
            }

            $partsOfExpr[] = array(
                "type of data" => "$currentToken->tokenClass",
                "data" => "$currentToken->bodyOfToken"
            );
            $currentToken = getNextToken();
        }
        array_pop($partsOfExpr);
        $previousNonterminal->loopCondition->partsOfExpression = $partsOfExpr;
        return $previousNonterminal;
    } elseif ($previousNonterminal->typeOfNode === "conditional jump operator if") {
        $previousNonterminal->ifSTMTCondition->typeOfExpression = "conditional if expression";

        $currentToken = getNextToken();
        $endOfExpr = "{";

        $strNum = $currentToken->NumOfStringInProgram;
        $isExprClosed = false;
        //считываем выражение
        while ($currentToken->bodyOfToken !== $endOfExpr && $currentToken->NumOfStringInProgram === $strNum) {

            if( $currentToken->tokenClass === "id"){
                $existsOrNot = $subTable->checkIfExists("", $currentToken->bodyOfToken);
                $arrayOrNot = $subTable->checkIfArray($currentToken->bodyOfToken);

                //ump($existsOrNot);
                if(!$existsOrNot){
                    $errorMessage = "\033[31m" . $currentToken->bodyOfToken . " without definition in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                } elseif ($arrayOrNot){
                    $errorMessage = "\033[31m" . "Syntax error: " . $currentToken->bodyOfToken . " is array, in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }
            }

            $partsOfExpr[] = array(
                "type of data" => "$currentToken->tokenClass",
                "data" => "$currentToken->bodyOfToken"
            );

            $currentToken = getNextToken();
        }

        //копируем собранное выражение в узел
        array_pop($partsOfExpr);
        $previousNonterminal->ifSTMTCondition->partsOfExpression = $partsOfExpr;
        return $previousNonterminal;
    }

    return $previousNonterminal;
}


function inputOrOutputNode($previousNonterminal, $currentParent, $currentToken, $nestingLevelCounter): object
{
    global $syntaxErrorHandler;
    global $tokenArrayIndex;
    global $subTable;
    global $currentTable;

    //функция
    $calleeFunction = new AstLibFuncClass($nestingLevelCounter);
    $calleeFunction->typeOfNode = "Calling a library function";
    $calleeFunction->bodyOfNode = $currentToken->bodyOfToken;
    $calleeFunction->NumOfStringInProgram = $currentToken->NumOfStringInProgram;

    //пропускаем открывающую скобку

    $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
    $syntaxErrorHandler->checkForSyntaxErrors("inOrOut", $currentToken);
    getNextToken();
    $currentToken = getNextToken();
    //формат
    $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
    getNextToken();
    $currentToken = getNextToken();


    if ($calleeFunction->bodyOfNode === "printf") {

        //переменные для вывода

        while ($currentToken->bodyOfToken !== ")" && $currentToken->bodyOfToken !== ";") {

            //добавляем запятые в качестве разделителя в массив
            if ($currentToken->bodyOfToken === ",") {
                $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
                $currentToken = getNextToken();
                continue;
            }

            if( $currentToken->tokenClass === "id"){
                $existsOrNot = $subTable->checkIfExists("", $currentToken->bodyOfToken);
                //ump($existsOrNot);
                if(!$existsOrNot){
                    $errorMessage = "\033[31m" . $currentToken->bodyOfToken . " without definition in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }
            }

            if ($currentToken->tokenClass === "id" || $currentToken->tokenClass === "numeric_constant" || $currentToken->tokenClass === "l_sqparen" || $currentToken->tokenClass === "r_sqparen") {
                //выводится переменная либо массив
                $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
                $currentToken = getNextToken();
            }
        }
    } elseif ($calleeFunction->bodyOfNode === "scanf") {

        //переменные для ввода

        while ($currentToken->bodyOfToken !== ")") {
            //добавляем запятые в качестве разделителя в массив
            if ($currentToken->bodyOfToken === ",") {
                $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
                $currentToken = getNextToken();
                continue;
            }
            if ($currentToken->tokenClass === "bitwise_and") {
                $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
                $currentToken = getNextToken();
            }

            if ($currentToken->tokenClass === "id"){
                $existsOrNot = $subTable->checkIfExists("", $currentToken->bodyOfToken);
                //ump($existsOrNot);
                if(!$existsOrNot){
                    $errorMessage = "\033[31m" . $currentToken->bodyOfToken . " without definition in line " . $currentToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }
            }

            if ($currentToken->tokenClass === "id" || $currentToken->tokenClass === "l_sqparen" || $currentToken->tokenClass === "r_sqparen") {
                //выводится переменная либо массив
                $calleeFunction->callableArguments[] = $currentToken->bodyOfToken;
                $currentToken = getNextToken();
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

function keyWordReturnNode($previousNonterminal, $currentParent, $currentToken, $nestingLevelCounter)
{
    global $syntaxErrorHandler;
    global $tokenArrayIndex;

    $returnNode = new InstructionKeyWordClass($nestingLevelCounter);
    $returnNode->typeOfNode = "KeyWord return";
    $returnNode->bodyOfNode = "return";
    $returnNode->NumOfStringInProgram = $currentToken->NumOfStringInProgram;
    $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
    $syntaxErrorHandler->checkForSyntaxErrors("return", $currentToken);
    $currentToken = getNextToken();
    $returnNode->returnValue = $currentToken->bodyOfToken;

    if (isset($currentParent->childNode) && ($returnNode->nestingLevel > $currentParent->nestingLevel)) {
        $previousNonterminal->nextNode = $returnNode;
        $returnNode->parentNode = $currentParent;
    } elseif (isset($currentParent->childNode) && ($returnNode->nestingLevel < $currentParent->nestingLevel)) {
        while ($returnNode->nestingLevel < $currentParent->nestingLevel) {
            $currentParent = $currentParent->parentNode;
        }
        $currentParent->nextNode = $returnNode;
        $returnNode->parentNode = $currentParent->parentNode;
    } elseif (isset($currentParent->childNode) && $returnNode->nestingLevel === $currentParent->nestingLevel) {
        $currentParent->nextNode = $returnNode;
        $returnNode->parentNode = $currentParent->parentNode;
    } else {
        $currentParent->childNode = $returnNode;
        $returnNode->parentNode = $currentParent;
    }


    return $returnNode;
}

function assigmentNode($previousNonterminal, $currentParent, $currentToken, $nestingLevelCounter): object
{
    global $syntaxErrorHandler;
    global $tokenArrayIndex;
    global $currentTable;
    global $subTable;

    $assigmentNode = new AstAssigmentClass($nestingLevelCounter);
    $assigmentNode->variableToAssigning->id = $currentToken->bodyOfToken;
    $typeOfId = $subTable->getTypeOfVariable($currentToken->bodyOfToken);
    $assigmentNode->variableToAssigning->dataType = $typeOfId;
    $assigmentNode->symbolTable = $subTable;
    $assigmentNode->NumOfStringInProgram = $currentToken->NumOfStringInProgram;

    $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
    $syntaxErrorHandler->checkForSyntaxErrors("assigment", $currentToken);
    $syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
    $syntaxErrorHandler->checkForSyntaxErrors("init braces check", $currentToken);
    $currentToken = getNextToken();
    if ($currentToken->bodyOfToken === "[") {
        for ($i = 0; $i < 3; $i++) {
            $assigmentNode->variableToAssigning->id .= $currentToken->bodyOfToken;
            $currentToken = getNextToken();
        }
    }

    if ($currentToken->bodyOfToken === "=") {
        $assigmentNode->typeOfNode = "Variable assignment expression";
        //$syntaxErrorHandler->setParametrs($tokenArrayIndex, $nestingLevelCounter);
        //$syntaxErrorHandler->checkForSyntaxErrors("init braces check", $currentToken);
        $assigmentNode = expressionNode($assigmentNode, $currentToken, $nestingLevelCounter);


        if (isset($currentParent->childNode) && ($assigmentNode->nestingLevel > $currentParent->nestingLevel)) {
            $previousNonterminal->nextNode = $assigmentNode;
            $assigmentNode->parentNode = $currentParent;
        } elseif (isset($currentParent->childNode) && ($assigmentNode->nestingLevel < $currentParent->nestingLevel)) {
            while ($assigmentNode->nestingLevel < $currentParent->nestingLevel) {
                $currentParent = $currentParent->parentNode;
            }
            $currentParent->nextNode = $assigmentNode;
            $assigmentNode->parentNode = $currentParent->parentNode;
        } elseif (isset($currentParent->childNode) && $assigmentNode->nestingLevel === $currentParent->nestingLevel) {
            $currentParent->nextNode = $assigmentNode;
            $assigmentNode->parentNode = $currentParent->parentNode;
        } else {
            $currentParent->childNode = $assigmentNode;
            $assigmentNode->parentNode = $currentParent;
        }
    }

    return $assigmentNode;
}



function getNextToken()
{
    global $Token;
    global $tokenArrayIndex;
    global $nestingLevelCounter;
    global $currentTable;
    global $subTable;

    $currentToken = $Token[$tokenArrayIndex++];
    if ($currentToken->bodyOfToken === "{") {
        $nestingLevelCounter++;

        if($Token[$tokenArrayIndex - 2]->bodyOfToken !== "="){
            //var_dump($Token[$tokenArrayIndex - 2]);
            $currentTable = $subTable;
            //echo "+1 nestLVL";
            //print_r($subTable->parentTable);
        }

    } elseif ($currentToken->bodyOfToken === "}") {
        $nestingLevelCounter--;
        //var_dump($Token[$tokenArrayIndex - 2]->bodyOfToken);
        if($Token[$tokenArrayIndex - 2]->bodyOfToken === "\\n" || $Token[$tokenArrayIndex - 2]->bodyOfToken === ";"){
            $currentTable = $subTable->parentTable;
            //var_dump($Token[$tokenArrayIndex - 2]);
            //echo "-1 nestLVL";
            //print_r($currentTable);
        }

    }

    return $currentToken;

}

function checkNextToken(){
    global $Token;
    global $tokenArrayIndex;
    global $nestingLevelCounter;

    $currentToken = $Token[$tokenArrayIndex];
    return $currentToken;
}


