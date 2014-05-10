<?php
namespace WScore\functionalTests\UsersModel;

use ArrayObject;

class UserEntity extends ArrayObject
{
    public function __construct()
    {
        parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
    }
}