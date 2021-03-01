<?php
$parseTable[][] = array();

$parseTable['PROG']['KeyWord if'];
$parseTable['PROG']['KeyWord while'];
$parseTable['PROG']['printf'];
$parseTable['PROG']['scanf'];
$parseTable['PROG']['l_brace'];
$parseTable['PROG']['id'];
$parseTable['PROG']['LParen'];

$parseTable['STMT']['KeyWord if'];
$parseTable['STMT']['KeyWord while'];
$parseTable['STMT']['printf'];
$parseTable['STMT']['scanf'];
$parseTable['STMT']['l_brace'];
$parseTable['STMT']['id'];
$parseTable['STMT']['LParen'];

$parseTable['ELSE\'']["KeyWord else"];
$parseTable['ELSE\'']["KeyWord else if"];
$parseTable['ELSE\'']["endOfInput"];
$parseTable['ELSE\'']["r_brace"];

$parseTable['P_EXPR']['LParen'];

$parseTable['EXPR']['LParen'];
$parseTable['EXPR']['id'];

$parseTable['EXPR\'']['plus'];
$parseTable['EXPR\'']['semi'];
$parseTable['EXPR\'']['RParen'];

$parseTable['TERM']['LParen'];
$parseTable['TERM']['id'];

$parseTable['TERM\'']['multiply'];
$parseTable['TERM\'']['plus'];
$parseTable['TERM\'']['semi'];
$parseTable['TERM\'']['RParen'];


$parseTable['F']['LParen'];
$parseTable['F']['id'];

$parseTable['idOrFunc']['int'];
$parseTable['idOrFunc']['char'];
$parseTable['idOrFunc']['float'];
$parseTable['idOrFunc']['double'];
$parseTable['idOrFunc']['void'];
$parseTable['idOrFunc']['short'];

$parseTable['OPERATOR\'']['='];
$parseTable['OPERATOR\'']['('];

$parseTable['PROC_DIR']['#include'];
$parseTablep['PROC_DIR']['#define'];
