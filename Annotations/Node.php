<?php
namespace SCM\ProcessBundle\Annotations;

/**
 * @Annotation
 */
class Node
{
    /**
     * @Enum({"step", "gateway", "userAction"})
     */
    private $type="step";
    private $description;

    public function __construct($options)
    {
        if (isset($options['value'])) {
            $options['description'] = $options['value'];
            unset($options['value']);
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }

    public function getType(){
        return $this->type;
    }

    public function getDescription(){
        return $this->description;
    }


}