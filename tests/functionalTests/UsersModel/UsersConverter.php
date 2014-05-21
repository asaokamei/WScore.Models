<?php
namespace WScore\functionalTests\UsersModel;

use WScore\Models\Dao\Converter;

class UsersConverter extends Converter
{
    /**
     * @param int $value
     * @return UserStatus
     */
    protected function muteStatusAttribute( $value )
    {
        return new UserStatus($value);
    }

    /**
     * @param string $value
     * @return UserGender
     */
    protected function muteGenderAttribute( $value )
    {
        return new UserGender($value);
    }
}