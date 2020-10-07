<?php
declare(strict_types=1);
set_include_path("D:\OPENSERVER\OpenServer\domains\compile\src\LexicalAnalyzeClasses");
require_once 'include.php';


function myLexer($handler, array $tokenArr): array
{
    $con = 0;
    $positionInStrIndex = 0;
    $stringCounter = 0;
    $programCopy = array();
    $arrayIndexOfCurrentElement = 0;
    $indexOfNextElement = 0;

    $LexerObj = new LexicalAnalyze();
    $TokenFormObj = new TokenFormation();
    $CompleteTokenObj = new CompleteToken();


    if ($handler === false) {
        exit("Ошибка при открытии файла");
    }
    do {
        $symbol = fgetc($handler);

        $programCopy[] = $symbol;
    } while ($symbol !== false);


    $indexOfLastElement = count($programCopy);
    //print($indexOfLastElement);
    //print_r((string)$programCopy[$indexOfLastElement-2]);
    $indexOfLastElement = $indexOfLastElement - 2;
    //print ("\n");
    if ($programCopy[$indexOfLastElement] === false) {
        array_pop($programCopy);
        $indexOfLastElement = end($programCopy);
        echo $indexOfLastElement;
    }

    print ("\n");

    do {
        $symbol = $programCopy[$arrayIndexOfCurrentElement];
        $indexOfNextElement++;

        if ($symbol === "\n") {

            $CompleteTokenObj = new CompleteToken();
            $CompleteTokenObj->setParameters("\\n", "NextLineSymbol", $positionInStrIndex, $positionInStrIndex, $stringCounter);
            $positionInStrIndex = 0;
            $stringCounter++;
            $arrayIndexOfCurrentElement = $indexOfNextElement;

            if (is_object($CompleteTokenObj)) {

                $tokenArr[$con] = clone($CompleteTokenObj);
                $con += 1;
            }

            unset($CompleteTokenObj);
            continue;
        }

        if ($symbol === " ") {
            $positionInStrIndex++;
            $arrayIndexOfCurrentElement = $indexOfNextElement;
            continue;
        }

        if ($symbol === "\t") {
            $positionInStrIndex++;
            $arrayIndexOfCurrentElement = $indexOfNextElement;
            continue;
        }


        if ($LexerObj->letter($symbol)) {

            $CompleteTokenObj = $TokenFormObj->letterToken($symbol, $arrayIndexOfCurrentElement, $programCopy, $stringCounter, $positionInStrIndex);

            $arrayIndexOfCurrentElement += $CompleteTokenObj->endPositionInString - $CompleteTokenObj->startPositionInString;
            $indexOfNextElement += $CompleteTokenObj->endPositionInString - $CompleteTokenObj->startPositionInString;
            $positionInStrIndex += $CompleteTokenObj->endPositionInString - $CompleteTokenObj->startPositionInString + 1;
        } elseif ($LexerObj->digit($symbol)) {

            $CompleteTokenObj = $TokenFormObj->digitToken($symbol, $arrayIndexOfCurrentElement, $programCopy, $stringCounter, $positionInStrIndex);

            $arrayIndexOfCurrentElement += $CompleteTokenObj->endPositionInString - $CompleteTokenObj->startPositionInString;
            $indexOfNextElement += $CompleteTokenObj->endPositionInString - $CompleteTokenObj->startPositionInString;
            $positionInStrIndex += $CompleteTokenObj->endPositionInString - $CompleteTokenObj->startPositionInString + 1;
        } elseif (!ctype_space($symbol)) {

            $CompleteTokenObj = $TokenFormObj->allOtherTokens($symbol, $arrayIndexOfCurrentElement, $programCopy, $stringCounter, $positionInStrIndex);

            $arrayIndexOfCurrentElement += $CompleteTokenObj->endPositionInString - $CompleteTokenObj->startPositionInString;
            $indexOfNextElement += $CompleteTokenObj->endPositionInString - $CompleteTokenObj->startPositionInString;
            $positionInStrIndex += $CompleteTokenObj->endPositionInString - $CompleteTokenObj->startPositionInString + 1;
        }

        $arrayIndexOfCurrentElement = $indexOfNextElement;

        if (is_object($CompleteTokenObj)) {

            $tokenArr[$con] = clone($CompleteTokenObj);
            $con += 1;
        }

        unset($CompleteTokenObj);
        //yield $CompleteTokenObj;
    } while ($indexOfNextElement <= $indexOfLastElement);


    fclose($handler);
    return $tokenArr;
    //
}

/*
$getNextToken = 'myLexer';
$handler = fopen("tests/FindMinArrayElement.c", "r");
$tokenArr = array();
$tokenArr = $getNextToken($handler, $tokenArr);
foreach ($tokenArr as $tok){
    if($tok->tokenClass === "NextLineSymbol") {
        print_r($tok);
    }
}*/


