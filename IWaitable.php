<?php
namespace Srdestino\Waitable;

interface IWaitable
{
    /**
     * It will pause execution until the Waitable is resolved (i.e., fulfilled or rejected),and to resume execution of
     * the asynchronous function after fulfillment.
     * When resumed it will return the value will be the value of the fullfilment.
     */
    public function Wait();

    /**
     * Ends the waitable with a given value
     * */
    public function Resolve($value);

    /**
     * @param $err
     * It will reject the waitable with the given error.
     * It will notify all the callbacks added in then and will set the waitable as rejected.
     */
    public function Reject($err);

    /**
     * @param IWaitable $waitable
     * It will pause execution until all the Waitables are resolved (i.e., fulfilled or rejected)
     */
    public static function WaitAll($waitList);
}