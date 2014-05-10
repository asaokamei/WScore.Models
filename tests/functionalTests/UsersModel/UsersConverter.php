<?php
namespace WScore\functionalTests\UsersModel;

use WScore\DbGateway\Converter;

class UsersConverter extends Converter
{
    protected $entityClass = '\WScore\functionalTests\UsersModel\UserEntity';

}