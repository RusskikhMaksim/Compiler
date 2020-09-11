<?php
function ifStatementNode($previousNonterminal, $currentParent, $currentToken, $nestingLevelCounter)
{
    $ifStatementNode = new AstIfClass($nestingLevelCounter);

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