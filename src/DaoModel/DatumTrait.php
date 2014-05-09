<?php
namespace WScore\DbGateway\DaoModel;

/**
 * Class DatumTrait
 *
 * to be used in Dao class.
 *
 * @package WScore\DbGateway\DaoModel
 */
trait DatumTrait
{
    // +----------------------------------------------------------------------+
    //  Basic CRUD for Datum.
    // +----------------------------------------------------------------------+
    /**
     * @param $data
     * @return bool|string
     */
    public function addDatum( $data )
    {
        $this->data = $data;
        $this->hooks( 'addDatum' );
        return $this->insert( $data );
    }

    /**
     * @param $id
     * @param $data
     * @return int
     */
    public function modDatum( $id, $data )
    {
        $this->data = $data;
        $this->hooks( 'modDatum' );
        $this->lastQuery->where( $this->primaryKey, '=', $id );
        return $this->update( $data );
    }

    /**
     * @param $id
     * @return array
     */
    public function getDatum( $id )
    {
        $this->lastQuery->where( $this->primaryKey, '=', $id );
        $data = $this->select();
        if( count( $data ) === 1 ){
            return $data[0];
        }
        return array();
    }

    /**
     * @param string $id
     */
    public function delDatum( $id )
    {
        $this->hooks( 'modDatum', $id );
        $this->query()->delete($id);
    }
}