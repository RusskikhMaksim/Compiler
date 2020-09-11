<?php
function keyWordReturnNode($previousNonterminal, $currentParent, $currentToken, $nestingLevelCounter)
{

    $returnNode = new InstructionKeyWordClass($nestingLevelCounter);
    $returnNode->typeOfNode = "KeyWord return";
    $returnNode->bodyOfNode = "return";
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