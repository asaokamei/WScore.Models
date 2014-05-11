<?php
namespace WScore\functionalTests\UsersModel;

use WScore\DbGateway\Enum\AbstractEnum;

class UserStatus extends AbstractEnum
{
    const ACTIVE  = 1;
    const PENDING = 5;
    const DELETED = 9;
    
    static $choices = [
        self::PENDING => 'just applied',
        self::ACTIVE  => 'active member',
        self::DELETED => 'inactive',
    ];
}

