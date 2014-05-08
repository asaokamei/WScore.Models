Enum in PHP
===========

Yet another implementation of enum type in PHP.

inspired (or copied idea) from:
[https://github.com/myclabs/php-enum](https://github.com/myclabs/php-enum)
and
[https://github.com/fre5h/DoctrineEnumBundle](https://github.com/fre5h/DoctrineEnumBundle).

Enum: Wishing Code
------------------

### creating a enum class.

Declaring a class for enums.

```php
class Gender extends Enum
{
    const MALE   = 'M';
    const FEMALE = 'F';

    protected static $choices = [
        self::MALE   => 'Male',
        self::FEMALE => 'Female',
    ];
}
```

### how to use it.

create a enum variable by constructing the enum class. and then, ...

```php
$someOneGender = new Gender( Gender::FEMALE );

// getting the value set
echo (string) $someOneGender;   // 'F'
echo $someOneGender->get();     // 'F'
echo $someOneGender->show();    // 'Female'

// checking the value
echo $someOneGender->is( Gender::MALE );   // false;
echo $someOneGender->is( Gender::FEMALE ); // true;
echo $someOneGender->isMale();             // false;
echo $someOneGender->isFemale();           // true;

// getting the list of possible values
echo $someOneGender::exists( 'X' );        // false;
$list = Gender::getValues(); // [ 'M', 'F' ]
$list = Gender::getChoices();   // [ 'M' => 'Male', 'F' => 'Female' ]
```

that's it.

### some more feature.

probably this is not necessary. or even somewhat dangerous.

```php
$someOneGender = new Gender( Gender::FEMALE );
$someOneElseGender = new Gender( Gender::FEMALE );
$someOneGender ==  $someOneElseGender; // true.
$someOneGender === $someOneElseGender; // true !?
```

EnumList Wishing Code
---------------------

```php
class Choices extends EnumList
{
    const ADD = 'A';
    const MOD = 'M';
    const DEL = 'D';
    protected static $choices = [
        self::ADD => 'Adding',
        self::MOD => 'Modifying',
        self::DEL => 'Deleting'
    ];
    protected static $separator = ',';
}
```

```php
$choice = new Choices( "A,M" );

// getting the values
echo (string) $choice;  // "A,M"
echo $choice->get();    // "A,M"
echo $choice->show();   // [ 'Adding', 'Modifying' ]
echo $choice->list();   // [ 'A', 'M' ]

// checking the value
echo $choice->is( Choices::DEL ); // false;
echo $choice->is( Choices::ADD ); // true;
echo $choice->isDel();            // false;
echo $choice->isMod();            // true;

// getting the list of possible values
$list = Choices::getConstList(); // [ 'A', 'M', 'D' ]
$list = Choices::getChoices();   // [ 'A' => 'Adding', 'M' => 'Modifying', 'D' => 'Deleting' ]
```