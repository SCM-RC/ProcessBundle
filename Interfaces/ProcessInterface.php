<?php
namespace SCM\ProcessBundle\Interfaces;

use SCM\ProcessBundle\Annotations\Event;
use SCM\ProcessBundle\Annotations\Next;
use SCM\ProcessBundle\Annotations\Node;

interface ProcessInterface
{
    /**
     * @Node(type="start",description="Process start")
     * @Next("plusNode")
     * @param $data
     * @return
     */
    public function startNode($data);

    /**
     * @Node(type="end",description="Process end")
     * @param $data
     * @return mixed
     */
    public function endNode($data);

    /**
     * @Event(eventName="defaultError")
     * @param $data
     * @param \Exception $exception
     * @return mixed
     */
    public function errorEvent($data,\Exception $exception);


}