<?php
function keyWordReturnNode($previousNonterminal, $currentParent, $currentToken, $testObj, $nestingLevelCounter){

    $returnNode = new InstructionKeyWordClass($nestingLevelCounter);
    $returnNode->typeOfNode = "KeyWord return";
    $returnNode->bodyOfNode = "return";
    $currentToken = NextToken($testObj);
    $returnNode->returnValue = $currentToken->bodyOfToken;

    if(isset($currentParent->childNode)){
        $previousNonterminal->nextNode = $returnNode;
    }
    else{
        $currentParent->childNode = $returnNode;
    }

    $returnNode->parentNode = $currentParent;

    return $returnNode;
}