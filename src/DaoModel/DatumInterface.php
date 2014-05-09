<?php
namespace WScore\DbGateway\DaoModel;

interface DatumInterface
{
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

}