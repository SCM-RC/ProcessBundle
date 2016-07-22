<?php
namespace SCM\ProcessBundle\Process;


use Doctrine\ORM\EntityManager;
use SCM\ProcessBundle\Entity\ProcessFlow;

class PersistedProcess extends BaseProcess
{
    /**
     * Runs node and persists results
     * @param string $node Node name
     * @param string $data Data to process
     * @return mixed
     */
    public function run($node, $data){
        $result = $this->$node($data);
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();
        $flow = new ProcessFlow();
        $flow
            ->setProcessNode($node)
            ->setDataIn($data)
            ->setDataOut($result)
            ->setProcess( $em->getReference('SCMProcessBundle:Process', $this->pid))
            ->setStartedBy($this->get('security.token_storage')->getToken()->getUsername())
            ->setTimestamp(new \DateTime());

        $em->persist($flow);
        $em->flush();

        return $result;
    }

}