# Waitable

The Waitable class is used for asynchronous computing. A Waitable object has three states 
- Fulfilled
- Rejected
- Running: this is the default state when it's created.

This class is analogue to JavaScript Promise class


# Usage

## Create a new Waitable

``` php
$waitable = new Waitable(function (IWaitable $waitable){
  LongTask();
  $waitable->Resolve(null); // When u call this method it will mark the Waitable as Fullfied
});
```

This will create a new Waitable and the function we pass to the constructor will be executed asynchronously

## Wait until the Waitable is resolved

``` php

$waitable = new Waitable(function (IWaitable $waitable){
  $waitable->Resolve("Test"); // When u call this method it will mark the waitable as Fullfied
});

$result = $waitable->Wait();
echo $result; // It will print "Test"

```

## Wait until all promises are resolved

``` php
$firstWaitable = new Waitable(function (IWaitable $waitable){
    $waitable->Resolve("First value");
});
$secondWaitable = new Waitable(function (IWaitable $waitable) use($secondValue){
    $promise->Resolve("Second value");
});
list($firstResult, $secondResult) = Waitable::WaitAll([$firstWaitable, $secondWaitable]);

echo $firstResult; // "First value"
echo $secondResult; // "Second value"
```

The static method WaitAll receives an array of IWaitable and wait untill all are fullfiled,  this method will return an array with the values returned by all the waitables

