<?php
namespace WScore\DbGateway;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use RuntimeException;

/**
 * Class Dao
 * @package WScore\Dao
 *
 * methods supported by __call using QueryBuilder in Illuminate/Database.
 *
 * @method  $this   paginate
 * @method  $this   first
 * @method  $this   latest
 * @method  $this   getProcessor
 * @method  $this   orWhere
 * @method  $this   take
 * @method  $this   unionAll
 * @method  $this   getPaginationCount
 * @method  $this   whereDay
 * @method  $this   orWhereNotExists
 * @method  $this   lock
 * @method  $this   orWhereNotBetween
 * @method  $this   insertGetId
 * @method  $this   orderByRaw
 * @method  $this   lists
 * @method  $this   find
 * @method  $this   sharedLock
 * @method  $this   offset
 * @method  $this   whereIn
 * @method  $this   groupBy
 * @method  $this   forPage
 * @method  $this   whereNested
 * @method  $this   getGrammar
 * @method  $this   from
 * @method  $this   whereNotIn
 * @method  $this   sum
 * @method  $this   remember
 * @method  $this   orWhereBetween
 * @ method    update
 * @method  $this   raw
 * @method  $this   oldest
 * @method  $this   orderBy
 * @method  $this   mergeWheres
 * @method  $this   mergeBindings
 * @method  $this   havingRaw
 * @ method    select
 * @ method    get
 * @method  $this   orHavingRaw
 * @method  $this   getCached
 * @method  $this   avg
 * @method  $this   whereExists
 * @method  $this   generateCacheKey
 * @method  $this   getBindings
 * @method  $this   skip
 * @method  $this   orWhereRaw
 * @method  $this   where
 * @method  $this   joinWhere
 * @method  $this   newQuery
 * @method  $this   count
 * @method  $this   selectRaw
 * @ method    insert
 * @method  $this   orWhereNotNull
 * @method  $this   having
 * @method  $this   exists
 * @method  $this   addBinding
 * @method  $this   addNestedWhereQuery
 * @method  $this   simplePaginate
 * @method  $this   join
 * @method  $this   whereRaw
 * @method  $this   max
 * @method  $this   min
 * @method  $this   orWhereIn
 * @method  $this   whereNull
 * @method  $this   dynamicWhere
 * @method  $this   getConnection
 * @ method    delete
 * @method  $this   union
 * @method  $this   whereNotNull
 * @method  $this   cacheDriver
 * @method  $this   orWhereExists
 * @method  $this   whereMonth
 * @method  $this   getCacheKey
 * @method  $this   lockForUpdate
 * @method  $this   cacheTags
 * @method  $this   truncate
 * @method  $this   implode
 * @method  $this   whereNotBetween
 * @method  $this   leftJoin
 * @method  $this   orWhereNull
 * @method  $this   addSelect
 * @method  $this   getFresh
 * @method  $this   rememberForever
 * @method  $this   increment
 * @method  $this   limit
 * @method  $this   whereYear
 * @method  $this   orHaving
 * @method  $this   decrement
 * @method  $this   leftJoinWhere
 * @method  $this   getRawBindings
 * @method  $this   whereNotExists
 * @method  $this   orWhereNotIn
 * @method  $this   setBindings
 * @method  $this   buildRawPaginator
 * @method  $this   toSql
 * @method  $this   distinct
 * @method  $this   pluck
 * @method  $this   whereBetween
 * @method  $this   aggregate
 * @method  $this   chunk
 */
class DaoArray implements DaoInterface
{
    /**
     * @var Manager
     */
    protected $db;

    /**
     * @var Builder
     */
    protected $query;

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
    protected $date_formats = 'Y-m-d H:i:s';

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
     */
    public function __construct( $db )
    {
        $this->hooks( 'constructing' );
        $this->db = $db;

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
        $this->query();
        $this->hooks( 'constructed' );
    }

    /**
     * @param bool $new
     * @return Builder
     */
    public function query( $new=true )
    {
        if( $new ) {
            $this->query = $this->db->table( $this->table );
            $this->hooks( 'newQuery' );
        }
        return $this->query;
    }

    /**
     * @param $method
     * @param $args
     * @return $this
     * @throws RuntimeException
     */
    public function __call( $method, $args )
    {
        if( method_exists( $this, $scope = 'scope'.ucfirst($method) ) ) {
            call_user_func_array( [$this, $scope], $args );
            return $this;
        }
        elseif( $this->query && method_exists( $this->query, $method ) ) {
            $returned = call_user_func_array( [$this->query, $method ], $args );
            if( is_object( $returned ) ) {
                return $this;
            }
            return $returned;
        }
        throw new RuntimeException( 'no such method: '.$method );
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
     * - constructing, constructed, newQuery,
     * - selecting, selected, inserting, inserted,
     * - updating, updated, deleting, deleted,
     *
     * @param string $event
     */
    protected function hooks( $event )
    {
        if( method_exists( $this, $scope = 'on'.ucfirst($event) ) ) {
            $args = func_get_args();
            array_shift($args);
            call_user_func_array( [$this, $scope], $args );
        }
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
        // insert data
        $this->hooks( 'inserting', $data );
        if( $this->insertSerial ) {
            $id = $this->query->insertGetId( $data );
            $data[ $this->primaryKey ] = $id;
        } else {
            $this->query->insert( $data );
            $id = true;
        }
        $this->hooks( 'inserted', $data );
        $this->query();
        return $id;
    }

    /**
     * alternative parameters: update( $id, $data )
     *
     * @param array $data
     * @return int
     */
    public function update( $data )
    {
        if( func_num_args() >= 2 ) {
            $id = func_get_arg(0);
            $data = func_get_arg(1);
            $this->setId( $id );
        }
        $this->updateTimeStamps( $data );
        // update data
        $this->hooks( 'updating', $data );
        $ok = $this->query->update( $data );
        $this->hooks( 'updated', $data );
        $this->query();
        return $ok;
    }

    /**
     * @param array $columns
     * @return array
     */
    public function select( $columns=array('*') )
    {
        $this->hooks( 'selecting' );
        $data = $this->query->select( $columns )->get();
        // select data
        $this->hooks( 'selected', $data );
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
        $result = $this->query->delete($id);
        $this->hooks( 'deleted', $id );
        $this->query();
        return $result;
    }

    /**
     * @param string|array $id
     */
    public function setId($id)
    {
        if( is_array($id) ) {
            $id = $id[$this->primaryKey];
        }
        $this->query->where( $this->primaryKey, '=', $id );
    }

    /**
     * @param array $data
     * @param bool $insert
     */
    protected function updateTimeStamps( &$data, $insert=false )
    {
        $now = $this->getCurrentTime();
        $this->updated_at    && $data[$this->updated_at]   = $now->format($this->date_formats);
        $this->updated_date  && $data[$this->updated_date] = $now->format('Y-m-d');
        $this->updated_time  && $data[$this->updated_time] = $now->format('H:i:s');
        if( !$insert ) return;

        $this->created_at    && $data[$this->created_at]   = $now->format($this->date_formats);
        $this->created_date  && $data[$this->created_date] = $now->format('Y-m-d');
        $this->created_time  && $data[$this->created_time] = $now->format('H:i:s');
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
        return array_keys( (array) $data );
    }

    // +----------------------------------------------------------------------+
}