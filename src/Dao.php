<?php
namespace WScore\DbGateway;


use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;

/**
 * Class Dao
 * @package WScore\Dao
 *
 * @method    paginate
 * @method    first
 * @method    latest
 * @method    getProcessor
 * @method    orWhere
 * @method    take
 * @method    unionAll
 * @method    getPaginationCount
 * @method    whereDay
 * @method    orWhereNotExists
 * @method    lock
 * @method    orWhereNotBetween
 * @method    insertGetId
 * @method    orderByRaw
 * @method    lists
 * @method    find
 * @method    sharedLock
 * @method    offset
 * @method    whereIn
 * @method    groupBy
 * @method    forPage
 * @method    whereNested
 * @method    getGrammar
 * @method    from
 * @method    whereNotIn
 * @method    sum
 * @method    remember
 * @method    orWhereBetween
 * @ method    update
 * @method    raw
 * @method    oldest
 * @method    orderBy
 * @method    mergeWheres
 * @method    mergeBindings
 * @method    havingRaw
 * @ method    select
 * @ method    get
 * @method    orHavingRaw
 * @method    getCached
 * @method    avg
 * @method    whereExists
 * @method    generateCacheKey
 * @method    getBindings
 * @method    skip
 * @method    orWhereRaw
 * @method    where
 * @method    joinWhere
 * @method    newQuery
 * @method    count
 * @method    selectRaw
 * @ method    insert
 * @method    orWhereNotNull
 * @method    having
 * @method    exists
 * @method    addBinding
 * @method    addNestedWhereQuery
 * @method    simplePaginate
 * @method    join
 * @method    whereRaw
 * @method    max
 * @method    min
 * @method    orWhereIn
 * @method    whereNull
 * @method    dynamicWhere
 * @method    getConnection
 * @ method    delete
 * @method    union
 * @method    whereNotNull
 * @method    cacheDriver
 * @method    orWhereExists
 * @method    whereMonth
 * @method    getCacheKey
 * @method    lockForUpdate
 * @method    cacheTags
 * @method    truncate
 * @method    implode
 * @method    whereNotBetween
 * @method    leftJoin
 * @method    orWhereNull
 * @method    addSelect
 * @method    getFresh
 * @method    rememberForever
 * @method    increment
 * @method    limit
 * @method    whereYear
 * @method    orHaving
 * @method    decrement
 * @method    leftJoinWhere
 * @method    getRawBindings
 * @method    whereNotExists
 * @method    orWhereNotIn
 * @method    setBindings
 * @method    buildRawPaginator
 * @method    toSql
 * @method    distinct
 * @method    pluck
 * @method    whereBetween
 * @method    aggregate
 * @method    chunk
 */
class Dao implements DaoInterface
{
    /**
     * @var Manager
     */
    protected $db;

    /**
     * @var Converter
     */
    protected $convert;

    /**
     * @var Builder
     */
    protected $lastQuery;

    /**
     * table name of the db.
     *
     * @var string
     */
    protected $table;

    /**
     * primary key (id) of the table.
     *
     * @var string
     */
    protected $primaryKey;

    /**
     * set to false when using insert, instead of insertId.
     *
     * @var string
     */
    protected $insertSerial = 'insertGetId';

    /**
     * datetime related format used in the database.
     *
     * @var array
     */
    protected $date_formats = array(
        'datetime' => 'Y-m-d H:i:s',
        'date'     => 'Y-m-d',
        'time'     => 'H:i:s',
    );

    /**
     * list of columns as array.
     *
     * @var array
     */
    protected $columns = array();

    /*
     * fields for automated datetime columns.
     */
    protected $created_at   = 'created_at';
    protected $created_date = '';
    protected $created_time = '';
    protected $updated_at   = 'updated_at';
    protected $updated_date = '';
    protected $updated_time = '';

    // +----------------------------------------------------------------------+
    //  managing object.
    // +----------------------------------------------------------------------+
    /**
     * @param Manager $db
     * @param Converter $convert
     */
    public function __construct( $db, $convert )
    {
        $this->hooks( 'constructing' );
        $this->db = $db;
        $this->convert = $convert;

        if( !$this->table ) {
            $name = get_class($this);
            if( false!==strpos($name, '\\') ) {
                $name = substr( $name, strrpos($name,'\\')+1 );
            }
            $this->table = $name;
        }
        if( !$this->primaryKey ) {
            $this->primaryKey = $this->table . '_id';
        }
        $this->convert->setDao( $this );
        $this->convert->setDateTime( $this->created_at,   $this->date_formats['datetime'] );
        $this->convert->setDateTime( $this->updated_at,   $this->date_formats['datetime'] );
        $this->query();
        $this->hooks( 'constructed' );
    }

    /**
     * @return Builder
     */
    public function query()
    {
        $this->lastQuery = $this->db->table( $this->table );
        $this->hooks( 'newQuery' );
        return $this->lastQuery;
    }

    /**
     * @param $method
     * @param $args
     * @return $this
     * @throws \RuntimeException
     */
    public function __call( $method, $args )
    {
        if( $this->lastQuery && method_exists( $this->lastQuery, $method ) ) {
            call_user_func_array( [$this->lastQuery, $method ], $args );
            return $this;
        }
        elseif( method_exists( $this, $scope = 'scope'.ucfirst($method) ) )
        {
            call_user_func_array( [$this, $scope], $args );
            return $this;
        }
        throw new \RuntimeException( 'no such method: '.$method );
    }

    /**
     * bad method! must rewrite!
     *
     * @return \DateTime
     */
    protected function getCurrentTime()
    {
        static $now;
        if( !$now ) $now = new \DateTime();
        return $now;
    }

    /**
     * dumb hooks for various events. $data are all string.
     * available events are:
     * - creating, created, newQuery,
     * - selecting, selected, inserting, inserted,
     * - updating, updated, deleting, deleted,
     *
     * @param string $event
     * @param array  $data
     */
    protected function hooks( $event, &$data=array() )
    {
        /* example of a hook.
        if( $event == 'updating' ) {
            $this->lastQuery->lockForUpdate();
        }
        */
    }

    // +----------------------------------------------------------------------+
    //  Basic CRUD methods.
    // +----------------------------------------------------------------------+
    /**
     * @param array $data
     * @return bool
     */
    public function insert( $data )
    {
        $this->updateTimeStamps( $data, true );
        $this->hooks( 'inserting', $data );
        $values = $this->toString( $data );
        if( $this->insertSerial ) {
            $id = $this->lastQuery->insertGetId( $values );
            $this->convert->set( $values, $this->primaryKey, $id );
            $this->convert->set( $data, $this->primaryKey, $id );
        } else {
            $this->lastQuery->insert( $values );
            $id = true;
        }
        $this->hooks( 'inserted', $values );
        $this->query();
        return $id;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function update( $data )
    {
        $this->updateTimeStamps( $data );
        $this->hooks( 'updating', $data );
        $values = $this->toString( $data );
        $ok = $this->lastQuery->update( $values );
        $this->hooks( 'updated', $values );
        $this->query();
        return $ok;
    }

    /**
     * @param array $columns
     * @return bool|string
     */
    public function select( $columns=array() )
    {
        $this->hooks( 'selecting' );
        $data = $this->lastQuery->select( $columns )->get();
        $this->hooks( 'selected', $data );
        foreach( $data as &$td ) { // danger!
            $this->toObject( $td );
        }
        $this->query();
        return $data;
    }

    /**
     * @param string $id
     * @return int
     */
    public function delete($id=null)
    {
        $this->hooks( 'deleting', $id );
        $result = $this->lastQuery->delete($id);
        $this->hooks( 'deleted', $id );
        $this->query();
        return $result;
    }

    /**
     * @param $data
     * @param bool $insert
     */
    protected function updateTimeStamps( &$data, $insert=false)
    {
        $now = $this->getCurrentTime();
        if( $this->updated_at ) {
            $data[$this->updated_at] = $now->format($this->date_formats['datetime']);
        }
        if( $this->updated_date ) {
            $data[$this->updated_date] = $now->format($this->date_formats['date']);
        }
        if( $this->updated_time ) {
            $data[$this->updated_time] = $now->format($this->date_formats['time']);
        }
        if( !$insert ) return;

        if( $this->created_at ) {
            $data[$this->created_at] = $now->format($this->date_formats['datetime']);
        }
        if( $this->created_date ) {
            $data[$this->created_date] = $now->format($this->date_formats['date']);
        }
        if( $this->created_time ) {
            $data[$this->created_time] = $now->format($this->date_formats['time']);
        }
    }

    // +----------------------------------------------------------------------+
    //  Basic CRUD for Datum.
    // +----------------------------------------------------------------------+
    /**
     * @param $data
     * @return bool|string
     */
    public function addDatum( $data )
    {
        return $this->insert( $data );
    }

    /**
     * @param $id
     * @param $data
     * @return bool|string
     */
    public function modDatum( $id, $data )
    {
        $this->lastQuery->where( $this->primaryKey, '=', $id );
        return $this->insert( $data );
    }

    /**
     * @param $id
     * @return bool|string
     */
    public function getDatum( $id )
    {
        $this->lastQuery->where( $this->primaryKey, '=', $id );
        return $this->select();
    }

    /**
     * @param string $id
     */
    public function delDatum( $id )
    {
        $this->delete($id);
    }
    // +----------------------------------------------------------------------+
    //  managing data
    // +----------------------------------------------------------------------+
    /**
     * @param array $data
     * @return array
     */
    public function getColumns( $data=array() )
    {
        if( $this->columns ) return $this->columns;
        return array_keys( $data );
    }

    /**
     * @param array $data
     * @return array|object
     */
    protected function toObject( $data )
    {
        return $this->convert->toEntity( $data );
    }

    /**
     * @param $data
     * @return array|mixed|string
     */
    protected function toString( $data )
    {
        return $this->convert->toArray( $data );
    }

    // +----------------------------------------------------------------------+
}