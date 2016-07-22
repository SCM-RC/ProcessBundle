<?php
/**
 * Created by PhpStorm.
 * User: chukin
 * Date: 7/21/16
 * Time: 2:01 PM
 */

namespace SCM\ProcessBundle\Process;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseProcess implements ContainerAwareInterface
{

    use ContainerAwareTrait;
    protected $name;
    protected $pid;

    public function __construct(ContainerInterface $container, $name, $pid)
    {
        $this->setContainer($container);
        $this->name = $name;
        $this->pid = $pid;
    }


    /**
     * Gets a container service by its id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Returns true if the service id is defined.
     *
     * @param string $id The service id
     *
     * @return bool true if the service id is defined, false otherwise
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    public function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }

    protected function getName(){
        return $this->name;
    }

    protected function getPID(){
        return $this->pid;
    }

}