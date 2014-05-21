<?php
namespace tests\relationTests\BlogModels;

use WScore\Models\Enum\AbstractEnum;

class AuthorGender extends AbstractEnum
{
    const MALE    = 'M';
    const FEMALE  = 'F';

    static $choices = [
        self::MALE    => 'male',
        self::FEMALE  => 'female',
    ];
}

