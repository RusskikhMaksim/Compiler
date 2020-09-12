<?php
function preprocessorDirectiveNodeFunc(object $currentParent, object $currentToken, $testObj, $nestingLevelCounter): object
{

    $keyWordIncludeNode = new AstPreprocessorDirectiveClass($nestingLevelCounter);
    $keyWordIncludeNode->typeOfNode = "Preprocessor directive include";
    $keyWordIncludeNode->directive = $currentToken->bodyOfToken;
    $calleeLib = "";


    for ($i = 0; $i < 5; $i++) {
        $currentToken = NextToken($testObj);
        $calleeLib .= $currentToken->bodyOfToken;
    }
    $keyWordIncludeNode->calleeLib = $calleeLib;
    $currentParent->childNode = $keyWordIncludeNode;
    $keyWordIncludeNode->parentNode = $currentParent;

    return $keyWordIncludeNode;
}


function NextToken($obj)
{
    //global $Token;
    //global $tokenArrayIndex;
    //global $nestingLevelCounter;
    $currentToken = $obj->Token[$obj->tokenArrayIndex++];
    if ($currentToken->bodyOfToken === "{") {
        $obj->nestingLevelCounter++;
    } elseif ($currentToken->bodyOfToken === "}") {
        $obj->nestingLevelCounter--;
    }

    return $currentToken;

    //return $obj->Token[$obj->tokenArrayIndex++];

}
