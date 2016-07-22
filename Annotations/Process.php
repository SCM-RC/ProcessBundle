<?php

namespace SCM\ProcessBundle\Annotations;

/**
 * @Annotation
 * @package SCM\ProcessBundle\Annotations
 */
class Process
{

    private $name;
    private $type = 'managed';
    private $nodes = [];

    /**
     * Process constructor.
     * @param $options
     */
    public function __construct($options)
    {
        if (isset($options['value'])) {
            $options['name'] = $options['value'];
            unset($options['value']);
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }
            $this->$key = $value;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getNodes(){
        return $this->nodes;
    }
    
    public function setNodes(array $nodes){
        $this->nodes = $nodes;
    }

    public function addNode($method,$annotation){
        $this->nodes[$method] = $annotation;
    }
    
}