<?php
namespace SCM\ProcessBundle\Annotations;

/**
 * @Annotation
 */
class Event
{

    private $eventName;

    public function __construct($options)
    {
        if (isset($options['value'])) {
            $options['eventName'] = $options['value'];
            unset($options['value']);
        }

//        foreach ($options as $key => $value) {
//            if (!property_exists($this, $key)) {
//                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
//            }
//
//            $this->$key = $value;
//        }
    }

    public function getEvent(){
        return $this->eventName;
    }


}