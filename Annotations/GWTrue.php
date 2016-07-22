<?php
namespace SCM\ProcessBundle\Annotations;

/**
 * @Annotation
 */
class GWTrue
{
    private $next;

    public function __construct($options)
    {
        if (isset($options['value'])) {
            $options['next'] = $options['value'];
            unset($options['value']);
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }

    /**
     * Next node to process
     * @return mixed
     */
    public function getNode(){
        return $this->next;
    }
}