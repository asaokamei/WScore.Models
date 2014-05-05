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

    // +----------------------------------------------------------------------+
    //  constructing queries
    // +----------------------------------------------------------------------+
    /**
     * sets table name and primary key.
     *
     * @param $table
     * @param $id_name
     * @return \WScore\DbAccess\Query
     */
    public function setTable( $table, $id_name );

    /**
     * sets where condition.
     *
     * @param string $column
     * @param string $value
     * @param string $type
     * @return $this
     */
    public function condition( $column, $value, $type = 'eq' );

    /**
     * @return mixed
     */
    public function forUpdate();

    // +----------------------------------------------------------------------+
    //  managing values
    // +----------------------------------------------------------------------+
    /**
     * @param $data
     * @return $this
     */
    public function setData( $data );

    /**
     * @param $name
     * @return mixed|null
     */
    public function getValue( $name=null );

    /**
     * @param $name
     * @param $value
     */
    public function setValue( $name, $value );

    /**
     * @param $name
     */
    public function delValue( $name );

    // +----------------------------------------------------------------------+
    //  executing queries 
    // +----------------------------------------------------------------------+
    /**
     * sets execution type.
     *
     * @param string $type
     * @return $this
     */
    public function setExecType( $type );

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
     * @return mixed
     */
    public function getResult();

    // +----------------------------------------------------------------------+
}