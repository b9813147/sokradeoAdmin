<?php


namespace App\Types\Record;


abstract class RecordType
{
    const   CREATE = 'C';
    const   UPDATE = 'U';
    const   DELETE = 'D';
    const   SHARE = 'S';
}
