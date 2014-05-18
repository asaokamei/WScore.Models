<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2014/05/18
 * Time: 11:03
 */
namespace WScore\Models\Dao;

interface RelationHasJoinInterface
{
    /**
     * @param $joinBy
     * @return $this
     */
    public function joinBy( $joinBy );

    /**
     * @param      $key
     * @param null $joinKey
     * @return $this
     */
    public function targetKey( $key, $joinKey = null );

    /**
     * @param      $key
     * @param null $joinKey
     * @return $this
     */
    public function sourceKey( $key, $joinKey = null );
}