<?php
namespace SCM\ProcessBundle\Converters;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\Reader;
use Metadata\ClassHierarchyMetadata;
use Metadata\MergeableClassMetadata;
use Metadata\MetadataFactoryInterface;
use SCM\ProcessBundle\Annotations\Process;
use SCM\ProcessBundle\Interfaces\ProcessInterface;

/**
 * Class ProcessFileConverter
 * @package SCM\ProcessBundle\Converters
 */
class ProcessFileConverter
{
    private $reader;

    /**
     * ProcessFileConverter constructor.
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Converts annotations to objects
     * @param $originalObject
     * @return Process
     * @throws AnnotationException
     * @throws \Exception
     */
    public function convert($originalObject)
    {
        if (!($originalObject instanceof ProcessInterface))
            throw new \Exception("Class must inplement ProcessInterface");

        $reflectionObject = new \ReflectionObject($originalObject);

        /** @var Process $process */
        $process = $this->reader->getClassAnnotation($reflectionObject, 'SCM\\ProcessBundle\\Annotations\\Process');

        if (null == $process)
            throw new AnnotationException("Process class must have @Process annotation!");

        foreach ($reflectionObject->getMethods() as $reflectionMethod) {
            // Get node annotations from process class
            $annotation = $this->reader->getMethodAnnotation($reflectionMethod,'SCM\\ProcessBundle\\Annotations\\Node');
            if (null !== $annotation) {

                switch ($annotation->getType()){
                    case "gateway":
                        $true = $this->reader->getMethodAnnotation($reflectionMethod,'SCM\\ProcessBundle\\Annotations\\GWTrue' );
                        $false = $this->reader->getMethodAnnotation($reflectionMethod,'SCM\\ProcessBundle\\Annotations\\GWFalse' );

                        if(null == $true || null == $false)
                            throw new AnnotationException('Node must have @BPGWTrue and @GWFalse Annotations');

                        $process->addNode($reflectionMethod->getName(),[
                            "type"=>$annotation->getType(),
                            "description"=>$annotation->getDescription(),
                            "true"=>$true->getNode(),
                            "false"=>$false->getNode()
                        ]);
                        break;
                    case "end":
                        $process->addNode($reflectionMethod->getName(),[
                            "type"=>$annotation->getType(),
                            "description"=>$annotation->getDescription(),
                            "next"=>false,
                        ]);
                        break;
                    default:
                        $next = $this->reader->getMethodAnnotation($reflectionMethod,'SCM\\ProcessBundle\\Annotations\\Next' );

                        if(null === $next)
                            throw new AnnotationException('Node must have @Next Annotation');

                        $process->addNode($reflectionMethod->getName(),[
                            "type"=>$annotation->getType(),
                            "description"=>$annotation->getDescription(),
                            "next"=>$next->getNode()
                        ]);
                        break;
                }
            }
        }

        return $process;
    }

}