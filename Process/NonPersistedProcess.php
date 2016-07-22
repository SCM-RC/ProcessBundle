<?php
/**
 * Created by PhpStorm.
 * User: chukin
 * Date: 7/21/16
 * Time: 1:08 PM
 */

namespace SCM\ProcessBundle\Process;

class NonPersistedProcess extends BaseProcess
{


    /**
     * Runs node
     * @param string $node Node name
     * @param string $data Data to process
     * @return mixed
     */
    public function run($node, $data){
        return $this->$node($data);
    }

}