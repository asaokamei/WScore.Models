WScore.DbGateway
================

Data Access Object, a gateway object to database storage. 

### License

MIT License

### required packages:

depends on "Illuminate/database", a Laravel's database component.

Laravel has an excellent ORM called Eloquent, but
I wanted a simple Data Access Object implementation.

Also, I needed something very configurable so that I can
use it for --outdated-- legacy projects.

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
    protected $table = 'our_user';
    protected $primaryKey = 'user_id';
    protected $columns = [ 'user_id', 'status', 'name',... ];
}
$capsule = new Manager();
$dao = new YourDao( $capsule );
```

Please refer to Illuminate/database for setting up "Capsule",
a database manager. please note:

*   if $table is not set, class name is used as table name. 
*   if $primaryKey is not set, tableName_id is used as primary key.
*   if $column is not set, dao tries to save all the data to db.

### Basic CRUD

```php
$data = $dao->where( 'X', '=', 'Y' )->select();
$dao->insert( $data );
$dao->where( 'X', '=', 'Y' )->update( [ 'A' => 'b' ] );
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

Converter for Entity and Value Object
-------------------------------------

Converter is an object for each dao, that converts an array
data to/from entity objects, as well as mutate the value into
value objects. (mutate is the word used in Laravel4...)

### Defining Value Objects

Value objects maybe any class but they must have either of
\_\_toString or format methods. They are used to convert the
value object to a string when saving data to db.

This repository contain a handy enum abstract class to
create a value object.

```php
class UserStatus extends AbstractEnum {
    const ACTIVE  = '1';
    const DELETED = '9'
    protected static $choices = [
        self::ACTIVE  => 'active user',
        self::DELETED => 'inactive user',
    ];
}
```

### Defining Entity Objects

Entity objects maybe any class but they must have setter/getter
methods or implements ArrayAccess to access their properties.

a sample implementation of an entity class is:

```php
class UserEntity extends ArrayObject {
    public function __construct() {
        parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
    }
    public function getUserId() {
        return $this->user_id;
    }
    public function getStatus() {
        return $this->status;
    }
    public function setStatus( UserStatus $value ) {
        $this->status = $value;
    }
}
```

use carmel case name for setter/getter (i.e. getCarmelCase);
the converter will find the appropriate name as necessary.


### Defining Converter

Finally, a converter class.

```php
class UsersConverter extends Converter {
    protected $entityClass = 'UserEntity';
    protected function setStatus( $value ) {
        return new UserStatus( $value );
    }
}
```

### Putting things together

```php
$dao = new YourDao( $capsule, new UsersConverter );

// get existing user entity. 
$user = $dao->load(1);
echo $user->status->show(); // echos 'inactive user'.
$user->setStatus( new UserStatus( UserStatus::ACTIVE ) );
$dao->save( $user );

// create a new user
$user = $dao->create( ['status'=> UserStatus::INACTIVE, 'name' => 'new user' ] );
$dao->save( $user );
```
