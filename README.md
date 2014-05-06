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

### Convert to Object and Entity

