<?php
function preprocessorDirectiveNodeFunc(object $currentParent, object $currentToken, $testObj, $nestingLevelCounter): object {
    $keyWordIncludeNode = new AstPreprocessorDirectiveClass($nestingLevelCounter);
    $keyWordIncludeNode->typeOfNode = "Preprocessor directive";
    $includeDirective = new AstPreprocessorDirectiveClass($nestingLevelCounter);
    $includeDirective->typeOfNode = "include directive";
    $includeDirective->bodyOfNode = $currentToken->bodyOfToken;
    $calleeLib = new AstPreprocessorDirectiveClass($nestingLevelCounter);
    $calleeLib->typeOfNode = "Callee Library";
    for ($i = 0; $i < 5; $i++) {
        $currentToken = NextToken($testObj);
        $calleeLib->bodyOfNode .= $currentToken->bodyOfToken;
    }

    $currentParent->childNode = $keyWordIncludeNode;

    $keyWordIncludeNode->parentNode = $currentParent;
    $keyWordIncludeNode->childNode = $includeDirective;

    $includeDirective->parentNode = $keyWordIncludeNode;

    $includeDirective->nextNode = $calleeLib;
    $calleeLib->parentNode = $keyWordIncludeNode;

    return $keyWordIncludeNode;
}

function NextToken($obj) {
    //global $Token;
    //global $tokenArrayIndex;
    //global $nestingLevelCounter;
    $currentToken = $obj->Token[$obj->tokenArrayIndex++];
    if($currentToken->bodyOfToken === "{"){
        $obj->nestingLevelCounter++;
    }
    elseif($currentToken->bodyOfToken === "}"){
        $obj->nestingLevelCounter--;
    }

    return $currentToken;

    //return $obj->Token[$obj->tokenArrayIndex++];

}
//0
if($i > 10){
    //11
    if($i < 15){
        //2
    } elseif($i == 15) {

    } else {
   //2
    }
    //1
}
//0
$i = $i + 1;