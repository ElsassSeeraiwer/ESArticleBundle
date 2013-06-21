<?php

namespace ElsassSeeraiwer\ESArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/article")
 */
class MainController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function viewAction()
    {
    }
}
