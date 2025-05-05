<?php

namespace App\Enums;

enum UnidadeMedida: string
{
    case UN = 'UN'; // unidade
    case KG = 'KG'; // quilograma
    case GR = 'GR'; // grama
    case L = 'L'; // litro
    case ML = 'ML'; // mililitro
    case M = 'M'; // metro
    case M2 = 'M2'; // metro quadrado
    case M3 = 'M3'; // metro cúbico
    case CX = 'CX'; // caixa
    case CT = 'CT'; // cartela
    case CJ = 'CJ'; // conjunto
    case DZ = 'DZ'; // duzia
    case PC = 'PC'; // peça

    public function label(): string
    {
        return match($this) {
            self::UN => 'Unidade',
            self::KG => 'Quilograma',
            self::GR => 'Grama',
            self::L => 'Litro',
            self::ML => 'Mililitro',
            self::M => 'Metro',
            self::M2 => 'Metro Quadrado',
            self::M3 => 'Metro Cúbico',
            self::CX => 'Caixa',
            self::CT => 'Cartela',
            self::CJ => 'Conjunto',
            self::DZ => 'Dúzia',
            self::PC => 'Peça',
        };
    }
}
