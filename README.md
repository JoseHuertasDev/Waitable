# Waitable

The Waitable class is used for asynchronous computing. A Waitable object has three states 
- Fulfilled
- Rejected
- Running: ``` This is the default state when it's created ```

This class is analogue to JavaScript Promise class

# Installation
You can install this library from Composer executing the following commands ```composer require srdestino/waitable``` then all you have to do is use this as in the following examples

# Usage

## Create a new Waitable

``` php
$waitable = new Waitable(function (IWaitable $waitable){
  LongTask();
  $waitable->Resolve(null); // When you call this method it will mark the Waitable as Fulfilled
});
```

This will create a new Waitable and the function we pass to the constructor will be executed asynchronously

## Wait until the Waitable is resolved

``` php

$waitable = new Waitable(function (IWaitable $waitable){
  $waitable->Resolve("Test"); // When you call this method it will mark the waitable as Fulfilled
});

$result = $waitable->Wait();
echo $result; // It will print "Test"

```

## Wait until all waitables are resolved

``` php
$firstWaitable = new Waitable(function (IWaitable $waitable){
    $waitable->Resolve("First value");
});
$secondWaitable = new Waitable(function (IWaitable $waitable) use($secondValue){
    $waitable->Resolve("Second value");
});
list($firstResult, $secondResult) = Waitable::WaitAll([$firstWaitable, $secondWaitable]);

echo $firstResult; // "First value"
echo $secondResult; // "Second value"
```

The static method WaitAll receives an array of IWaitable and wait until all are fulfilled. This method will return an array with the values returned by all the waitables.

# Collaborating & Getting Help

This is an open source library, so you can collaborate if you want, feel free to [create Pull Request](https://github.com/JoseHuertasDev/Waitable/pulls) or [open an issue](https://github.com/JoseHuertasDev/Waitable/issues) in case you need help or want to suggest something.


# Apache

If you want to enable pcntl in Apache you can refer to the following post I wrote in medium

https://medium.com/@jhuertasdeveloper/how-to-enable-pcntl-in-apache-56c6f6f0cbab
