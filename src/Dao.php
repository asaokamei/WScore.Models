<?php
namespace WScore\Dao;


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
class Dao
{
    /**
     * @var Manager
     */
    protected $db;

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
     * specify the format to convert an object to a string.
     * @var array
     */
    protected $formats = array();

    /**
     * list of columns as array.
     *
     * @var array
     */
    protected $columns = array();

    /**
     * specify how a value maybe converted to an object.
     *
     * usage:
     * [ name_of_column => converter ]
     * where converter is
     * - callable function (or closure object),
     * - method name in the model,
     * - class name to create as new.
     *
     * @var array
     */
    protected $converts = array();

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
     */
    public function __construct( $db )
    {
        $this->hooks( 'constructing' );
        $this->db = $db;

        if( !$this->table ) {
            $name = get_class($this);
            if( false!==strpos($name, '\\') ) {
                $name = substr( $name, strpos($name,'\\')+1 );
            }
            $this->table = $name;
        }
        if( !$this->primaryKey ) {
            $this->primaryKey = $this->table . '_id';
        }
        $this->_setTime( $this->created_at,   'datetime' );
        $this->_setTime( $this->updated_at,   'datetime' );
        $this->_setTime( $this->created_date, 'date' );
        $this->_setTime( $this->updated_date, 'date' );
        $this->_setTime( $this->created_time, 'time' );
        $this->_setTime( $this->updated_time, 'time' );
        $this->query();
        $this->hooks( 'constructed' );
    }

    /**
     * @param $name
     * @param $type
     */
    protected function _setTime( $name, $type ) {
        if( !$this->$name ) return;
        $this->formats[$name] = $this->date_formats[$type];
        $this->converts[$name] = 'getCurrentTime';
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
            $this->setRawAttribute( $values, $this->primaryKey, $id );
            $this->setRawAttribute( $data, $this->primaryKey, $id );
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
        $this->_updateTimeStamps( $data,
            [ $this->updated_at, $this->updated_date, $this->updated_time ]
        );
        if( !$insert ) return;

        $this->_updateTimeStamps( $data,
            [ $this->created_at, $this->created_date, $this->created_time ]
        );
    }

    /**
     * @param $data
     * @param $stamps
     */
    protected function _updateTimeStamps( &$data, $stamps )
    {
        foreach( $stamps as $col )
        {
            if( $col ) {
                $now = $this->getCurrentTime();
                $this->setRawAttribute( $data, $col, $now );
            }
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
     * get attribute from $data, while converting to a proper data type.
     *
     * @param $data
     * @param $name
     * @return mixed
     */
    public function get( $data, $name )
    {
        $value = $this->getRawAttribute( $data, $name );
        $value = $this->convertToObject( $name, $value );
        return $value;
    }

    /**
     * set attribute to $data, while converting to a proper data type.
     *
     * @param $data
     * @param $name
     * @param $value
     */
    public function set( &$data, $name, $value )
    {
        $value = $this->convertToObject( $name, $value );
        $this->setRawAttribute( $data, $name, $value );
    }

    /**
     * @param array|object $data
     * @param string $name
     * @param mixed $value
     */
    protected function setRawAttribute( &$data, $name, $value )
    {
        if( is_array( $data ) ) {
            $data[ $name ] = $value;
            return;
        }
        $method = 'set'.ucwords($name);
        if( is_object( $data ) && method_exists( $data, $method ) ) {
            $data->$method( $$value );
            return;
        }
        if( $data instanceof \ArrayAccess ) {
            $data[$name] = $value;
            return;
        }
    }

    /**
     * @param array|object $data
     * @param string $name
     * @return mixed
     */
    protected function getRawAttribute( $data, $name )
    {
        if( is_array( $data ) ) {
            return $data[ $name ];
        }
        $method = 'get'.ucwords($name);
        if( is_object( $data ) && method_exists( $data, $method ) ) {
            $data->$method();
        }
        if( $data instanceof \ArrayAccess ) {
            $data[$name];
        }
        return null;
    }

    /**
     * @param $data
     * @param null $name
     */
    protected function toObject( &$data, $name=null )
    {
        if( !$name ) {
            $list = $this->getColumns( $data );
            foreach( $list as $name ) {
                $this->toObject( $data, $name );
            }
            return;
        }
        $value = $this->getRawAttribute( $data, $name );
        $this->set( $data, $name, $value );
    }

    /**
     * @param $data
     * @param $name
     * @return array|mixed|string
     */
    protected function toString( &$data, $name=null )
    {
        if( !$name ) {
            $list = $this->getColumns( $data );
            $values = array();
            foreach( $list as $name ) {
                $values[$name] = $this->toString( $data, $name );
            }
            return $values;
        }
        $value = $this->getRawAttribute( $data, $name );
        if( is_object( $value ) ) {
            if( $value instanceof \DateTime ) {
                $format = isset( $this->formats[$name] ) ? $this->formats[$name]: '';
                $value = $value->format($format);
            }
            elseif( method_exists( $value, 'format' ) ) {
                $format = isset( $this->formats[$name] ) ? $this->formats[$name]: '';
                $value = $value->format($format);
            }
            elseif( method_exists( $value, '__toString' ) ) {
                $value = $value->__toString();
            }
            $this->setRawAttribute($data, $name, $value );
        }
        return $value;
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    protected function convertToObject( $name, $value )
    {
        if( !is_string( $value ) ) return $value;
        if( array_key_exists( $name, $this->converts ) ) {
            $converter = $this->converts[$name];
            if( is_callable( $converter ) ) {
                return $converter($value);
            }
            if( is_string( $converter ) && method_exists( $this, $converter ) ) {
                return $this->$converter($value);
            }
            if( is_string( $converter ) && class_exists( $converter ) ) {
                return new $converter($value);
            }
        }
        return $value;
    }
    // +----------------------------------------------------------------------+
}