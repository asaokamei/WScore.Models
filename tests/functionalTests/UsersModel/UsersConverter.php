<?php
namespace WScore\functionalTests\UsersModel;

use WScore\Models\Converter;

class UsersConverter extends Converter
{
    protected $entityClass = '\WScore\functionalTests\UsersModel\UserEntity';

    /**
     * @param int $value
     * @return UserStatus
     */
    protected function setStatus( $value )
    {
        return new UserStatus($value);
    }

    /**
     * @param string $value
     * @return UserGender
     */
    protected function setGender( $value )
    {
        return new UserGender($value);
    }
}