<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2014/05/18
 * Time: 11:11
 */
namespace WScore\Models\Dao;

interface RelationSetupInterface
{
    /**
     * @param string      $name
     * @param string      $target
     * @param string|null $targetKey
     * @param string|null $myKey
     */
    public function hasMany( $name, $target, $targetKey = null, $myKey = null );

    /**
     * @param string      $name
     * @param string      $target
     * @param string|null $targetKey
     * @param string|null $myKey
     */
    public function belongsTo( $name, $target, $targetKey = null, $myKey = null );

    /**
     * @param      $name
     * @param      $target
     * @return RelationHasJoinInterface
     */
    public function hasJoin( $name, $target );
}