<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ControlType: string implements hasLabel, hasColor
{
    case ADMINISTRATIVE = "Administrative";
    case TECHNICAL = "Technical";
    case PHYSICAL = "Physical";
    case OPERATIONAL = "Operational";
    case OTHER = "Other";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ADMINISTRATIVE => 'Administrative',
            self::TECHNICAL => 'Technical',
            self::PHYSICAL => 'Physical',
            self::OPERATIONAL => 'Operational',
            self::OTHER => 'Other',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ADMINISTRATIVE => 'primary',
            self::TECHNICAL => 'primary',
            self::PHYSICAL => 'primary',
            self::OPERATIONAL => 'primary',
            self::OTHER => 'primary',
        };
    }
}
