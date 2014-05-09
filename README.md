WScore.DbGateway
================

Data Access Object, a gateway object to database storage. 

### License

MIT License

### required packages:

depends on "Illuminate/database", a Laravel's database component.

Laravel has an excellent ORM called Eloquent, but
I wanted some thing more simple Data Access Object.

Also, I needed something very configurable so that I can
use it for old legacy projects.

Lots of nice ideas, such as scope, are from Laravel's code.


Installation
------------

use composer to get it as "wscore/dbgateway". name may change.


Basic Usage
-----------

### constructing of Dao

```php
class YourDao extends Dao
{
    protected $table = 'TableName';
    protected $primaryKey = 'The_Primary_Key';
}
$capsule = new Manager();
$dao = new YourDao(
    $capsule, new Converter()
)
```

Please refer to Illuminate/database for setting up "Capsule", a database manager. 

note:

*   if $table is not set, class name is used as table name. 
*   if $primaryKey is not set, tableName_id is used as primary key.

### CRUD

```php
$data = $dao->where( 'X', '=', 'Y' )->select();
$dao->insert( $data );
$dao->where( 'X', '=', 'Y' )->update( [ 'A' => 'b' ] );
```

simple access for data.

```php
$id = $dao->addDatum( $data );
$data = $dao->getDatum( $id );
$dao->modDatum( $id, $data );
```

Advanced Topic
--------------

### Scope

Scope is, essentially the same as Query Scope in Laravel framework.
In fact, it is almost a dead copy of how things work. It is a very
useful feature, so here's how to use it.

Create scope{Scope} methods in your Dao class.

```php
class YourDao extends Dao {
    protected function scopeType( $value ) {
        $this->lastQuery->where( 'type', '=', $value );
    }
}
$dao = new YourDao(..);
$list_of_M = $dao->type('M')->select();
```

### Hooks (events)

create on{Events} methods in your Dao class.

```php
class YourDao extends Dao {
    protected onUpdating( $value ) {
        // do your stuff.
    }
}
```

or extend hooks method like:

```php
protected function hooks( $event, $values=null ) {
    parent::hooks( $event, $values ); // make sure to call parent hooks.
    switch( $event ) {
        case 'inserted':
            // do some stuff.
            break;
    }
}
```

available events are:

*   constructing, constructed, newQuery, selecting, selected,
    inserting, inserted, updating, updated, deleting, deleted

### Convert to Object and Entity

