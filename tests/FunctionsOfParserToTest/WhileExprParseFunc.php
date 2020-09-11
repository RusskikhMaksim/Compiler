<?php
function whileLoopNode($previousNonterminal, $currentParent, $currentToken, $nestingLevelCounter)
{

    $whileLoopNode = new AstWhileClass($nestingLevelCounter);

    $whileLoopNode->typeOfNode = "while loop";
    $whileLoopNode->parentNode = $currentParent;

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
    $currentToken = getNextToken();

    if ($currentToken->bodyOfToken === "(") {

        $whileLoopNode = expressionNode($whileLoopNode, $currentToken, $nestingLevelCounter);

    }
    return $whileLoopNode;
}
