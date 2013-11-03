<?php
namespace WScore\DataMapper\Filter;

/**
 * Class FilterInterface
 *
 * @package WScore\DataMapper\Filter
 * @ method onQuery
 * @ method onInsert
 * @ method onUpdate
 * @ method onDelete
 * @ method onSave
 * @ method onRead
 * @ method onFetch
 * @ method setModel
 */
interface FilterInterface
{
    public function setModel( $model );
    public function apply( $event, $data );
}