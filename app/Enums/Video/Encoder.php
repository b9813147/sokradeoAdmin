<?php

namespace App\Enums\Video;

use BenSampo\Enum\Enum;

/**
 * @method static static DEFAULT()
 * @method static static FILE_UPLOAD()
 * @method static static HI_ENCODER()
 * @method static static I_ACTIVE()
 * @method static static A_REC()
 * @method static static AVA()
 */
final class Encoder extends Enum
{
    const DEFAULT = 'Default';
    const FILE_UPLOAD = 'FileUpload';
    const HI_ENCODER = 'HiEncoder';
    const I_ACTIVE = 'iActive';
    const A_REC = 'Arec';
    const AVA = 'AVA';
}
