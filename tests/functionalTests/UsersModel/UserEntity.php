<?php
namespace WScore\functionalTests\UsersModel;

use ArrayObject;

class UserEntity extends ArrayObject
{
    public function __construct($data=[])
    {
        parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
    }
}