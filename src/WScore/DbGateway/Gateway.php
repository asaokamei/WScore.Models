<?php
namespace WScore\DbGateway;

/**
 * base class for dao's for database tables.
 * a Table Data Gateway pattern.
 *
 */
class Gateway
{
    /**
     * @var string                          name of database table
     */
    protected $table;

    /**
     * @var string                          name of primary key
     */
    protected $id_name;

    /** 
     * @Inject
     * @var \WScore\DbGateway\Query  
     */
    public $query;

    /**
     * @Inject
     * @var \WScore\DbGateway\FilterManager
     */
    public $filters;
    
    // +----------------------------------------------------------------------+
    //  Managing Object and Instances. 
    // +----------------------------------------------------------------------+
    /**
     * @param null|string $table
     * @param null|string $id
     */
    public function __construct( $table=null, $id=null )
    {
        if( !$this->table ) $this->setTable( $table );
        if( !$this->id_name ) $this->setId( $id );
    }

    /**
     * @param null|string $table
     */
    public function setTable( $table=null )
    {
        if( !$table ) {
            $table = get_called_class();
            $table = substr( $table, strrpos( $table, '\\' ) );
        }
        $this->table   = $table;
    }

    /**
     * @param null|string $id
     */
    public function setId( $id=null )
    {
        if( !$id ) $id = $this->table . '_id';
        $this->id_name = $id;
    }

    /**
     * @return \WScore\DbGateway\Query
     */
    public function query() {
        // set fetch mode after query is cloned in table() method.
        return $this->query = $this->query->setTable( $this->table, $this->id_name );
    }

    /**
     * @return FilterManager
     */
    public function filters() {
        return $this->filters;
    }
    // +----------------------------------------------------------------------+
    //  Basic DataBase Access.
    // +----------------------------------------------------------------------+
    /**
     * fetches data for given primary key $id. 
     * 
     * @param $id
     * @return \PdoStatement
     */
    public function find( $id )
    {
        $this->query(); // reset query.
        return $this->fetch( $id, $this->id_name );
    }
    
    /**
     * fetches entities from simple condition.
     * use $select to specify column name to get only the column you want.
     * 
     * @param string|array $value
     * @param null         $column
     * @return \PdoStatement
     */
    public function fetch( $value=null, $column=null )
    {
        $query = $this->query;
        if( !$column         ) $column = $this->id_name;
        if( !is_null( $value ) ) {
            $query->condition( $column, $value, QueryInterface::EQUALS );
        }
        $query->setExecType( QueryInterface::SELECT );
        $this->filters->apply( 'query', $query );
        $query->execute(); 
        return $query->getResult();
    }

    // +----------------------------------------------------------------------+
    //  Basic DataBase Access.
    // +----------------------------------------------------------------------+
    /**
     * update data. update( $data ) or update( $data, $id ). 
     *
     * @param array        $data
     * @param null|string  $id
     * @return self
     */
    public function update( $data, $id=null )
    {
        if( !$id ) {
            $id = $data[ $this->id_name ];
            unset( $data[ $this->id_name ] );
        }
        $this->query()
            ->condition( $this->id_name, $id, QueryInterface::EQUALS )
            ->setData( $data )
            ->setExecType( QueryInterface::UPDATE );
        $this->filters->apply( 'update', $query );
        $this->query->execute();
        return $this;
    }

    /**
     * deletes an id.
     * override this method (i.e. just tag some flag, etc.).
     *
     * @param string $id
     */
    public function delete( $id )
    {
        $this->query()
            ->condition( $this->id_name, $id, QueryInterface::EQUALS )
            ->setExecType( QueryInterface::DELETE );
        $this->filters->apply( 'delete', $query );
        $this->query->execute();
    }

    // +----------------------------------------------------------------------+
    /**
     * @param $data
     * @return string
     */
    public function insert( &$data ) 
    {
        return $this->insertId( $data );
    }
    
    /**
     * insert data into database.
     *
     * @param array   $data
     * @return string|bool             id of the inserted data or true if id not exist.
     */
    public function insertValue( &$data )
    {
        $this->insertData( $data );
        $id = array_key_exists( $this->id_name, $data ) ? $data[$this->id_name] : true;
        return $id;
    }

    /**
     * @param array   $data
     * @return string                 id of the inserted data
     */
    public function insertId( &$data )
    {
        unset( $data[ $this->id_name ] );
        $this->insertData( $data );
        $id = $this->query->getLastId();
        $data[ $this->id_name ] = $id;
        return $id;
    }

    /**
     * @param array $data
     */
    public function insertData( $data )
    {
        $this->query()->setData( $data );
        $this->filters->apply( 'insert', $query );
        $this->query->execute();
    }
    // +----------------------------------------------------------------------+
}

