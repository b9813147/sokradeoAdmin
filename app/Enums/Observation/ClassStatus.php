<?php

namespace App\Enums\Observation;

use BenSampo\Enum\Enum;

/**
 * @method static static READY
 * @method static static START
 * @method static static END
 */
final class ClassStatus extends Enum
{
    /**
     * 上課狀態
     */
    const READY = 'R';
    const START = 'S';
    const END = 'E';
}
