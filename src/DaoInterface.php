<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2014/05/07
 * Time: 5:57
 */
namespace WScore\DbGateway;

use Illuminate\Database\Query\Builder;


/**
 * Class Dao
 *
 * @package WScore\Dao
 *
 */
interface DaoInterface
{
    /**
     * @return Builder
     */
    public function query();

    /**
     * @param array $data
     * @return bool
     */
    public function insert( $data );


    /**
     * @param array $data
     * @return $this
     */
    public function update( $data );


    /**
     * @param array $columns
     * @return bool|string
     */
    public function select( $columns=array() );


    /**
     * @param string $id
     * @return int
     */
    public function delete($id=null);

    /**
     * @param $data
     * @return bool|string
     */
    public function addDatum( $data );


    /**
     * @param $id
     * @param $data
     * @return bool|string
     */
    public function modDatum( $id, $data );


    /**
     * @param $id
     * @return bool|string
     */
    public function getDatum( $id );


    /**
     * @param string $id
     */
    public function delDatum( $id );

    /**
     * @param array $data
     * @return array
     */
    public function getColumns( $data=array() );
}