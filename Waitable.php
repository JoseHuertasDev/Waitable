<?php
namespace Srdestino\Waitable;

use RuntimeException;

class Waitable implements IWaitable
{
    public $ShmAtached;
    private $valueKey;
    private $errorKey;
    private $stateKey;

    /**
     * @param callable $exec
     * $exec to be executed by the constructor, during the process of constructing the new Waitable object.
     * The executor is custom code that ties an outcome to a waitable. You, the programmer, write the executor.
     * Its signature is expected to be:
     * function(IWaitable $waitable);
     */
    public function __construct(callable $callback)
    {
        $this->attach();
        $this->generateKeys();
        $pid = pcntl_fork();
        if ($pid === -1) {
            throw new RuntimeException("Could not create waitable");
        } else if($pid == 0){ //If its 0 it means that is the parent process
            echo "Entra acá, PID: $pid";
            $callback($this);
            posix_kill(getmypid(), SIGKILL );
        }
    }

    /**
     * Generates unique keys for each property of the waitable,
     * These properties are used to store in a shared memory
     * @return void
     */
    private function generateKeys()
    {
        $this->stateKey = hexdec(uniqid());
        $this->valueKey = hexdec(uniqid());
        $this->errorKey = hexdec(uniqid());
    }

    private function GenerateUniqueKey(string $prefix): string
    {
        $bytes = random_bytes(7);
        return $prefix . bin2hex($bytes);
    }

    /**
     * It will create a shared memory an attach to the current object
     * */
    private function attach()
    {
        $fileName = tempnam("/tmp", $this->GenerateUniqueKey("waitable"));
        $key = ftok($fileName, 'a');
        $this->ShmAtached = shm_attach($key);
    }

    private function SetValue($value)
    {
        shm_put_var($this->ShmAtached, (int)$this->valueKey, $value);    //store var
    }

    private function SetError($value)
    {
        shm_put_var($this->ShmAtached, (int)$this->errorKey, $value);    //store var

    }

    private function SetState($value)
    {
        shm_put_var($this->ShmAtached, (int)$this->stateKey, $value);    //store var
    }

    private function GetValue()
    {
        if ($this->has((int)$this->valueKey))
            return shm_get_var($this->ShmAtached, (int)$this->valueKey);
        return null;
    }
    /** Setters and getters **/

    function GetError()
    {
        if ($this->has((int)$this->errorKey))
            return shm_get_var($this->ShmAtached, (int)$this->errorKey);
        return null;
    }
    private function has(int $key): bool{
        return shm_has_var($this->ShmAtached, $key);
    }
    private function GetState()
    {
        if ($this->has((int)$this->stateKey))
            return shm_get_var($this->ShmAtached, (int)$this->stateKey);
        return null;
    }

    public function Wait()
    {
        while($this->GetState() !== 'fulfilled' && $this->GetState() !== 'rejected' && $this->GetValue() === null){
            usleep(10000);
        }
        $value = $this->GetValue();
        return $value;
    }

    public function Resolve($value)
    {
        $this->SetValue($value);
        $this->SetState("fulfilled");
        return $value;
    }

    public function Reject($err)
    {
        $this->SetError($err);
        $this->SetState("rejected");
        return $err;
    }


    public static function WaitAll($waitList)
    {
        $results = [];
        foreach ($waitList as $waitable){
            $results[] = $waitable->wait();
        }
        return $results;
    }
}