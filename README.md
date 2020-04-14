# PHP implicit interface checker
Simple reflection-based ~~library~~ function to check if a class matches an external interface (i.e. an interface
that is not implemented by this class, but probably having the same method signatures).

## Wait, but why?
This library was inspired by Go language and its concept of interfaces. In Go interfaces are
implemented implicitly, i.e. an object implements an interface when it provides the same method signatures 
as described in the interface. Whereas in PHP interfaces MUST 
be implemented explicitly and this fact somehow prevents proper code decoupling.

Let's say we have the following class:

```php
class DeepThought
{
    public function answer(string $life, int $universe, array $everything): int
    {
        return 42;
    }
}
```

It does not implement any interface. Let us coin an interface for it:

```php
interface DeepThoughtInterface
{
    public function answer(string $life, int $universe, array $everything): int;
}
```
    
Actually, the signature of method `answer()` in the class completely matches the one in the interface.
But we can't actually confirm that with any standard PHP tools.

```php
$instance = new DeepThought();
if ($instance instanceof DeepThoughtInterface::class) {
    echo 'Success';
} else {
    echo 'Failure';
}
```

We will always receive `Failure` message. And this is sad. 
But now we can use our ~~library~~ function  for checking:

```php
use function yaronius\ImplicitInterface\class_provides;

$instance = new DeepThought();
if (class_provides($instance, DeepThoughtInterface::class)) {
    echo 'Success';
} else {
    echo 'Failure';
}
```

Now we get `Success`! Finally!
    
## TODO

- make it kinda library =)
- more OOP 
- more tests
- benchmark performance impact
