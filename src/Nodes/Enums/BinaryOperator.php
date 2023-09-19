<?php

namespace RinhaDeCompilerPhp\Nodes\Enums;

enum BinaryOperator: string
{
    case ADD = 'Add';
    case SUB = "Sub";
    case MUL = "Mul";
    case DIV = "Div";
    case REM = "Rem";
    case EQ  = "Eq";
    case NEQ = "Neq";
    case LT  = "Lt";
    case GT  = "Gt";
    case LTE = "Lte";
    case GTE = "Gte";
    case AND = "And";
    case OR  = "Or";
}