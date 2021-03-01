<?php
include "php1.php";
include "php2.php";

//$str = 'abcd';
//$array = str_split($str);
//$str2 = str_replace("a", "", $str);
//echo $str[0];


function anagrams(string $word, array $words): array
{
    $symbolsOfWord = [];
    $arrayOfAnagrams = [];
    $copyOfWord = $word;
    $i = 0;

    while ($copyOfWord !== "") {
        if ($copyOfWord === "") {
            break;
        }
        $countOfCurrentSymbol = substr_count($copyOfWord, $copyOfWord[$i]);
        $symbolsOfWord["$copyOfWord[$i]"] = $countOfCurrentSymbol;
        $copyOfWord = str_replace($copyOfWord[$i], "", $copyOfWord);
    }

    foreach ($words as $string) {
        $symbolsOfString = [];
        $copyOfString = $string;
        $i = 0;

        while ($copyOfString !== "") {
            if ($copyOfString === "") {
                break;
            }
            $countOfCurrentSymbol = substr_count($copyOfString, $copyOfString[$i]);
            $symbolsOfString["$copyOfString[$i]"] = $countOfCurrentSymbol;
            $copyOfString = str_replace($copyOfString[$i], "", $copyOfString);


        }


        $isAnagram = TRUE;
        $keys = array_keys($symbolsOfWord);
        $keysOfInputArr = array_keys($symbolsOfString);

        if(count($keys) !== count($keysOfInputArr)){
            $isAnagram = FALSE;
        }
        foreach ($keys as $symbol) {
            if ((!isset($symbolsOfString["$symbol"])) || $symbolsOfWord["$symbol"] !== $symbolsOfString["$symbol"]) {
                $isAnagram = FALSE;
                break;
            }
        }
        if ($isAnagram === TRUE) {
            $arrayOfAnagrams[] = $string;
        }

    }
    return $arrayOfAnagrams;
}

//anagrams('abba', ['aabb', 'abcd', 'bbaa', 'dada']);

//$a = "aabccc";
//$b = "ccbaa";
//$infoA = count_chars($a, 1);
//$infoB = count_chars($b, 1);




    //array("a");
//if($infoA == $infoB){
//    echo "ravno";
//}

$arr = array(1);
$a =& $arr[0]; // $a и $arr[0] ссылаются на одно значение
$arr2 = $arr; // присвоение не по ссылке!
$arr2[0]++;

//$a = 1;
//$c = 2;
//$b =& $a;
//$a = 5;
//$b =& $c;
$f_a = 'b';

function is_solved(array $board): int {
    for($i = 0; $i < 3; $i++){
        $checkResult = checkSetOfCells($board[$i]);
        if ($checkResult === 1){
            return 1;
        } elseif ($checkResult === 2){
            return 2;
        }
    }

    for($j = 0; $j < 3; $j++){
        $checkResult = checkSetOfCells([$board[0][$j], $board[1][$j], $board[2][$j]]);
        if ($checkResult === 1){
            return 1;
        } elseif ($checkResult === 2){
            return 2;
        }
    }


        $checkResult = checkSetOfCells([$board[0][0], $board[1][1], $board[2][2]]);
        if ($checkResult === 1){
            return 1;
        } elseif ($checkResult === 2){
            return 2;
        }

    $checkResult = checkSetOfCells([$board[0][2], $board[1][1], $board[2][0]]);
    if ($checkResult === 1){
        return 1;
    } elseif ($checkResult === 2){
        return 2;
    }

    foreach ($board as $set){
        if($set[0] === 0 || $set[1] === 0 || $set[2] === 0){
            return -1;
        }
    }
    return 0;
}

function  checkSetOfCells(array $set) {
    $checkCells = function () use ($set){
        if($set[0] === 1 && $set[1] === 1 && $set[2] === 1){
            return 1;
        } elseif ($set[0] === 2 && $set[1] === 2 && $set[2] === 2){
            return 2;
        }
    };


    $checkSet = function ($set) use ($checkCells){
        $resultOfCheck = $checkCells($set);
        if ($resultOfCheck === 1){
            return 1;
        } elseif ($resultOfCheck === 2){
            return 2;
        }
    };

    $result = $checkSet($set);
    return $result;

}

