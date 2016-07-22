<?php
namespace SCM\ProcessBundle\Processors;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use SCM\ProcessBundle\Annotations\Process;
use SCM\ProcessBundle\Converters\ProcessFileConverter;
use SCM\ProcessBundle\Entity\ProcessFlow;
use SCM\ProcessBundle\Interfaces\ProcessInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Reads and executes process from file
 * @package SCM\ProcessBundle\Processors
 */
class FileProcessor
{
    use ContainerAwareTrait;

    /**
     * @var string Current process unique id
     */
    private $uuid;
    /**
     * @var ProcessInterface Current process instance
     */
    private $processInstance = null;
    /**
     * @var string Current process name
     */
    private $processName;
    /**
     * @var string Current process node
     */
    private $currentNode = "startNode";
    /**
     * @var integer Max process steps to run
     */
    private $maxIterations = 20;
    /**
     * @var Process Process metadata information
     */
    private $processMetadata = null;

    /**
     * @var \SCM\ProcessBundle\Entity\Process
     */
    private $processEntity;

    /**
     * Processor constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * Strarts process from initial node.
     * @param string $processShortcut Shortcut notation of process (e.g. Bundle:SomeProcess)
     * @param mixed $data Data to process
     * @return mixed
     */
    public function start($processShortcut, $data)
    {
        $this->processName = $processShortcut;
        return $this->run($data);
    }

    /**
     * Resumes persisted process
     * @param int $uuid Process id
     * @param mixed $data Data to process
     * @return mixed
     * @throws EntityNotFoundException
     */
    public function resume($uuid, $data)
    {
        $this->uuid = $uuid;
        $this->processName = $this->getProcessEntity()->getName();

        /** @var ProcessFlow $flow */
        $flow = $this->getProcessEntity()->getFlow()->last();
        $nodes = $this->readProcessMetadata()->getNodes();
        $this->setCurrentNode($nodes[$flow->getProcessNode()]['next']);
        return $this->run($data);
    }

    /**
     * Executes process with given data
     * @param mixed $data
     * @return mixed
     */
    private function run($data)
    {
        if (!$this->currentNode) {
            throw new \LogicException("Process is already finished!");
        }

        try {
            $nodes = $this->readProcessMetadata()->getNodes();
            while ($this->currentNode !== false) {

                if ($this->maxIterations == 0) {
                    throw new \LogicException("Maximum process steps exided!");
                }

                switch ($nodes[$this->currentNode]["type"]) {
                    case "gateway":
                        $this->currentNode = $this->getProcessInstance()->run($this->currentNode, $data) ?
                            $nodes[$this->currentNode]['true'] :
                            $nodes[$this->currentNode]['false'];
                        break;
                    case "userAction":
                        $data = $this->getProcessInstance()->run($this->currentNode, $data);
                        $this->setCurrentNode(false);
                        break;
                    /** @noinspection PhpMissingBreakStatementInspection */
                    case "end":
                        $data = $this->getProcessInstance()->run($this->currentNode, $data);
                        $this->setCurrentNode($nodes[$this->currentNode]['next']);
                        $this->finishProcess();
                        break;
                    default:
                        $data = $this->getProcessInstance()->run($this->currentNode, $data);
                        $this->setCurrentNode($nodes[$this->currentNode]['next']);
                        break;
                }
                $this->maxIterations--;
            }
            return $data;
        } catch (\Exception $e) {
            return $this->getProcessInstance()->errorEvent($data, $e);
        }
    }

    /**
     * Reads process metadata
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Exception
     */
    private function readProcessMetadata()
    {
        if ($this->processMetadata === null) {
            $reader = new AnnotationReader();
            $converter = new ProcessFileConverter($reader);
            $this->processMetadata = $converter->convert($this->getProcessInstance());
        }
        return $this->processMetadata;
    }

    /**
     * Returns process instance by shortcut
     * @return object
     */
    private function getProcessInstance()
    {
        if ($this->processInstance === null) {
            list($bundle, $process) = $this->parseShortcutNotation($this->processName);
            $bundleReflection = new \ReflectionClass($this->container->get('kernel')->getBundle($bundle));
            $processClass = $bundleReflection->getNamespaceName() . "\\Process\\" . $process;
            if (is_subclass_of($processClass, 'SCM\\ProcessBundle\\Process\\PersistedProcess')) {
                $this->uuid = $this->getProcessEntity()->getId();
            } else {
                $this->uuid = uniqid();
            }
            $this->processInstance = new $processClass($this->container, $this->processName, $this->uuid);
        }
        return $this->processInstance;
    }


    /**
     * Finishes process entry in DB
     */
    private function finishProcess()
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine')->getManager();
        $processEntity = $this->getProcessEntity();
        $processEntity->setEndedAt(new \DateTime());
        $em->persist($processEntity);
        $em->flush();
    }

    /**
     *  Persists process in DB
     * @return null|object|\SCM\ProcessBundle\Entity\Process
     * @throws EntityNotFoundException
     */
    private function getProcessEntity()
    {
        if (!$this->processEntity) {
            /** @var EntityManager $em */
            $em = $this->container->get('doctrine')->getManager();
            if (!$this->uuid) {
                $processEntity = new \SCM\ProcessBundle\Entity\Process();
                $processEntity
                    ->setStartedAt(new \DateTime())
                    ->setName($this->processName)
                    ->setStartedBy($this->container->get('security.token_storage')->getToken()->getUsername());
                $em->persist($processEntity);
                $em->flush();
                $this->uuid = $processEntity->getId();
            } else {
                $processEntity = $em->getRepository('SCMProcessBundle:Process')->find($this->uuid);
            }
            if (!$processEntity) {
                throw new EntityNotFoundException("Process not found");
            }
            $this->processEntity = $processEntity;
        }
        return $this->processEntity;
    }

    /**
     * Converts shortcut notation to bundle and process names
     * @param string $shortcut Process shortcut notation
     * @return array
     */
    protected function parseShortcutNotation($shortcut)
    {
        $process = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($process, ':')) {
            throw new \InvalidArgumentException(sprintf('The process name must contain a : ("%s" given)', $process));
        }

        return array(substr($process, 0, $pos), substr($process, $pos + 1));
    }

    /**
     * Sets node to process
     * @param string $currentNode
     */
    public function setCurrentNode($currentNode)
    {
        $this->currentNode = $currentNode;
    }

    /**
     * @param int $maxIterations
     * @return $this
     */
    public function setMaxSteps($maxIterations)
    {
        $this->maxIterations = $maxIterations;
        return $this;
    }

}