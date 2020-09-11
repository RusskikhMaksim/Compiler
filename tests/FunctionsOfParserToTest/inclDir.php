<?php
function preprocessorDirectiveNodeFunc(object $currentParent, object $currentToken, $nestingLevelCounter): object
{
    global $nestingLevelCounter;
    $keyWordIncludeNode = new AstPreprocessorDirectiveClass($nestingLevelCounter);
    $keyWordIncludeNode->typeOfNode = "Preprocessor directive include";
    $keyWordIncludeNode->directive = $currentToken->bodyOfToken;
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
