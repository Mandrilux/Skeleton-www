<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class IndexController extends AbstractController
{
    /**
    * @Route("/", methods={"GET"}, name="index")
    */

   public function Index(Request $request)
   {
      return $this->render('index.html.twig');
   }
}
