<?php
namespace WScore\Models;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use RuntimeException;
use WScore\Models\Dao\TimeStamp;
use WScore\Models\Entity\Magic;
use WScore\Models\Dao\Eloquent;

/**
 * Class Dao
 * @package WScore\Dao
 *
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

    /**
     * array of objects for hooks and filters
     * 
     * @var array
     */
    protected $hooks = array();

    /**
     * time stamps config.
     * [ type => [ column, format ], type => column ]
     * 
     * @var array
     */
    protected $timeStamps = array(
        'created_at' => [ 'created_at', 'Y-m-d H:i:s' ],
        'updated_at' => 'updated_at', 
    );

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
        $this->hooks[] = $this;
        $this->hooks[] = $stamp = new TimeStamp();
        $stamp->setTimeStamps( $this->timeStamps );
        $this->hooks( 'constructed' );
    }

    /**
     * @return Eloquent
     */
    public function q()
    {
        return $this;
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
     * dumb hooks for various events. $data are all string.
     * available events are:
     * - constructing, constructed, newQuery,
     * - selecting, selected, inserting, inserted,
     * - updating, updated, deleting, deleted,
     *
     * @param string $event
     * @param mixed|null   $data
     * @return mixed|null
     */
    protected function hooks( $event, $data=null )
    {
        $args = func_get_args();
        array_shift($args);
        foreach( $this->hooks as $hook ) {
            if( method_exists( $hook, $method = 'on'.ucfirst($event) ) ) {
                call_user_func_array( [$hook, $method], $args );
            }
            if( method_exists( $hook, $method = 'on'.ucfirst($event).'Filter' ) ) {
                $data = call_user_func_array( [$hook, $method], $args );
            }
        }
        return $data;
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
        $data = $this->hooks( 'inserting', $data );
        if( $this->insertSerial ) {
            $id = $this->query->insertGetId( $data );
            $data[ $this->primaryKey ] = $id;
        } else {
            $this->query->insert( $data );
            $id = Magic::get( $data, $this->primaryKey ) ?: true;
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
        // update data
        $data = $this->hooks( 'updating', $data );
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
        $data = $this->hooks( 'selected', $data );
        $this->query();
        return $data;
    }

    /**
     * @param string $id
     * @param null   $column
     * @return int
     */
    public function delete( $id=null, $column=null)
    {
        $this->hooks( 'deleting', $id );
        if( $id ) {
            if( $column ) {
                $this->query->where( $column, '=', $id );
            } else {
                $this->query->where( $this->primaryKey, '=', $id );
            }
        }
        $result = $this->query->delete();
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
     * @param null|string $id
     * @param null|string $column
     * @return array
     */
    public function load( $id=null, $column=null )
    {
        if( $id ) {
            if( $column ) {
                $this->query->where( $column, '=', $id );
            } else {
                $this->setId( $id );
            }
        }
        return $this->select();
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

    /**
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getKeyName() {
        return $this->primaryKey;
    }
    // +----------------------------------------------------------------------+
}