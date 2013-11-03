<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2013/11/03
 * Time: 20:30
 */
namespace WScore\DbGateway;

interface QueryInterface
{
    const SELECT = 'select';
    const DELETE = 'delete';
    const UPDATE = 'update';
    const INSERT = 'insert';
    
    const EQUALS    = 'eq';
    const LESS_THAN = 'lt';
    const GREATER_THAN = 'gt';
    const LESS_EQUAL = 'le';
    const GREATER_EQUAL = 'ge';
    const LIKE = 'like';
    const IS_NULL = 'isNull';
    const NOT_NULL = 'notNull';
    
    /**
     * sets execution type.
     *
     * @param string $type
     */
    public function setExecType( $type );

    /**
     * sets table name and primary key.
     *
     * @param $table
     * @param $id_name
     * @return \WScore\DbAccess\Query
     */
    public function setTable( $table, $id_name );

    /**
     * executes the query.
     *
     * @return \WScore\DbAccess\Query
     */
    public function execute();

    /**
     * gets the last inserted ID.
     *
     * @return string
     */
    public function getLastId();

    /**
     * sets where condition.
     *
     * @param string $column
     * @param string $value
     * @param string $type
     */
    public function condition( $column, $value, $type = self::EQUALS );

    /**
     * @return mixed
     */
    public function getResult();
}