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
 * @method  Dao   paginate
 * @method  Dao   first
 * @method  Dao   latest
 * @method  Dao   getProcessor
 * @method  Dao   orWhere
 * @method  Dao   take
 * @method  Dao   unionAll
 * @method  Dao   getPaginationCount
 * @method  Dao   whereDay
 * @method  Dao   orWhereNotExists
 * @method  Dao   lock
 * @method  Dao   orWhereNotBetween
 * @method  Dao   insertGetId
 * @method  Dao   orderByRaw
 * @method  Dao   lists
 * @method  Dao   find
 * @method  Dao   sharedLock
 * @method  Dao   offset
 * @method  Dao   whereIn
 * @method  Dao   groupBy
 * @method  Dao   forPage
 * @method  Dao   whereNested
 * @method  Dao   getGrammar
 * @method  Dao   from
 * @method  Dao   whereNotIn
 * @method  Dao   sum
 * @method  Dao   remember
 * @method  Dao   orWhereBetween
 * @ method    update
 * @method  Dao   raw
 * @method  Dao   oldest
 * @method  Dao   orderBy
 * @method  Dao   mergeWheres
 * @method  Dao   mergeBindings
 * @method  Dao   havingRaw
 * @ method    select
 * @ method    get
 * @method  Dao   orHavingRaw
 * @method  Dao   getCached
 * @method  Dao   avg
 * @method  Dao   whereExists
 * @method  Dao   generateCacheKey
 * @method  Dao   getBindings
 * @method  Dao   skip
 * @method  Dao   orWhereRaw
 * @method  Dao   where
 * @method  Dao   joinWhere
 * @method  Dao   newQuery
 * @method  Dao   count
 * @method  Dao   selectRaw
 * @ method    insert
 * @method  Dao   orWhereNotNull
 * @method  Dao   having
 * @method  Dao   exists
 * @method  Dao   addBinding
 * @method  Dao   addNestedWhereQuery
 * @method  Dao   simplePaginate
 * @method  Dao   join
 * @method  Dao   whereRaw
 * @method  Dao   max
 * @method  Dao   min
 * @method  Dao   orWhereIn
 * @method  Dao   whereNull
 * @method  Dao   dynamicWhere
 * @method  Dao   getConnection
 * @ method    delete
 * @method  Dao   union
 * @method  Dao   whereNotNull
 * @method  Dao   cacheDriver
 * @method  Dao   orWhereExists
 * @method  Dao   whereMonth
 * @method  Dao   getCacheKey
 * @method  Dao   lockForUpdate
 * @method  Dao   cacheTags
 * @method  Dao   truncate
 * @method  Dao   implode
 * @method  Dao   whereNotBetween
 * @method  Dao   leftJoin
 * @method  Dao   orWhereNull
 * @method  Dao   addSelect
 * @method  Dao   getFresh
 * @method  Dao   rememberForever
 * @method  Dao   increment
 * @method  Dao   limit
 * @method  Dao   whereYear
 * @method  Dao   orHaving
 * @method  Dao   decrement
 * @method  Dao   leftJoinWhere
 * @method  Dao   getRawBindings
 * @method  Dao   whereNotExists
 * @method  Dao   orWhereNotIn
 * @method  Dao   setBindings
 * @method  Dao   buildRawPaginator
 * @method  Dao   toSql
 * @method  Dao   distinct
 * @method  Dao   pluck
 * @method  Dao   whereBetween
 * @method  Dao   aggregate
 * @method  Dao   chunk
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
     * @param array $data
     * @return array
     */
    public function getColumns( $data=array() );
}