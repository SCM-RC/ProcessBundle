<?php

namespace SCM\ProcessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProcessFlow
 *
 * @ORM\Table(name="process_flow")
 * @ORM\Entity(repositoryClass="SCM\ProcessBundle\Repository\ProcessFlowRepository")
 */
class ProcessFlow
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Process
     * @ORM\ManyToOne(targetEntity="SCM\ProcessBundle\Entity\Process",inversedBy="flow")
     */
    private $process;

    /**
     * @var string
     *
     * @ORM\Column(name="processNode", type="string", length=255)
     */
    private $processNode;

    /**
     * @var string
     *
     * @ORM\Column(name="dataIn", type="object", nullable=true)
     */
    private $dataIn;

    /**
     * @var string
     *
     * @ORM\Column(name="dataOut", type="object", nullable=true)
     */
    private $dataOut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="startedBy", type="string", length=255)
     */
    private $startedBy;
    

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set processNode
     *
     * @param string $processNode
     *
     * @return ProcessFlow
     */
    public function setProcessNode($processNode)
    {
        $this->processNode = $processNode;

        return $this;
    }

    /**
     * Get processNode
     *
     * @return string
     */
    public function getProcessNode()
    {
        return $this->processNode;
    }

    /**
     * Set dataIn
     *
     * @param \stdClass $dataIn
     *
     * @return ProcessFlow
     */
    public function setDataIn($dataIn)
    {
        $this->dataIn = $dataIn;

        return $this;
    }

    /**
     * Get dataIn
     *
     * @return \stdClass
     */
    public function getDataIn()
    {
        return $this->dataIn;
    }

    /**
     * Set dataOut
     *
     * @param \stdClass $dataOut
     *
     * @return ProcessFlow
     */
    public function setDataOut($dataOut)
    {
        $this->dataOut = $dataOut;

        return $this;
    }

    /**
     * Get dataOut
     *
     * @return \stdClass
     */
    public function getDataOut()
    {
        return $this->dataOut;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return ProcessFlow
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set startedBy
     *
     * @param string $startedBy
     *
     * @return ProcessFlow
     */
    public function setStartedBy($startedBy)
    {
        $this->startedBy = $startedBy;

        return $this;
    }

    /**
     * Get startedBy
     *
     * @return string
     */
    public function getStartedBy()
    {
        return $this->startedBy;
    }

    /**
     * Set process
     *
     * @param \SCM\ProcessBundle\Entity\Process $process
     *
     * @return ProcessFlow
     */
    public function setProcess(\SCM\ProcessBundle\Entity\Process $process = null)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Get process
     *
     * @return \SCM\ProcessBundle\Entity\Process
     */
    public function getProcess()
    {
        return $this->process;
    }
}
