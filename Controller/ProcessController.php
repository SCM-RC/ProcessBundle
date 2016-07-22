<?php

namespace SCM\ProcessBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use PharmActionBundle\Entity\Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProcessController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/process/test")
     */
    public function indexAction()
    {
        $data = $this->get('scm_bp.processor')
            ->setMaxSteps(35)
            ->start('SCMProcessBundle:CustomTestProcess', -10);

        return new JsonResponse($data);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/process/continue/{id}")
     */
    public function cnAction($id)
    {
        $data = $this->get('scm_bp.processor')
            ->setMaxSteps(35)
            ->resume($id, 11);

        return new JsonResponse($data);
    }
}
