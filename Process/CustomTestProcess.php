<?php
namespace SCM\ProcessBundle\Process;

use SCM\ProcessBundle\Annotations\Event;
use SCM\ProcessBundle\Annotations\GWFalse;
use SCM\ProcessBundle\Annotations\GWTrue;
use SCM\ProcessBundle\Annotations\Next;
use SCM\ProcessBundle\Annotations\Node;
use SCM\ProcessBundle\Annotations\Process;
use SCM\ProcessBundle\Interfaces\ProcessInterface;

/**
 * Class CustomTestProcess
 * @Process("TestProcess")
 * @package SCM\ProcessBundle\Process
 */
class CustomTestProcess extends PersistedProcess implements ProcessInterface
{

    /**
     * @Node(type="start",description="Process start")
     * @Next("plusNode")
     * @param $data
     * @return mixed
     */
    public function startNode($data)
    {
        return $data + 1;
    }

    /**
     * @Node("Adds 5")
     * @Next("minusNode")
     * @param $data
     * @return mixed
     */
    public function plusNode($data)
    {
        return $data + 5;
    }

    /**
     * @Node("Substracts 3")
     * @Next("ifMoreThan10")
     * @param $data
     * @return mixed
     */
    public function minusNode($data)
    {
        return $data - 3;
    }

    /**
     * @Node(type="gateway",description="Check if more than 10")
     * @GWTrue("userActionNode")
     * @GWFalse("plusNode")
     * @param $data
     * @return bool
     */
    public function ifMoreThan10($data)
    {
        return $data > 10;
    }

    /**
     * @Node(type="end",description="Process end")
     * @param $data
     * @return mixed
     */
    public function endNode($data)
    {
        return $data*2;
    }

    /**
     * @Node(type="userAction",description="UserAction step")
     * @Next("endNode")
     * @param $data
     * @return string
     */
    public function userActionNode($data){
        return "Waiting for user action!";
    }

    /**
     * @Event(eventName="defaultError")
     * @param $data
     * @param \Exception $exception
     * @return mixed
     * @throws \Exception
     */
    public function errorEvent($data, \Exception $exception)
    {
        throw new \Exception($exception->getMessage());
    }
}