<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;

class HistoryController extends ApiController
{
  private $serializer;

  public function __construct(SerializerInterface $serializer){
    $this->serializer= $serializer;
  }

    /**
    * @Route("/request", methods={"GET"}, name="get_request")
    */

   public function Index(Request $request)
   {
     $repository = $this->getDoctrine()
                  ->getManager()
                  ->getRepository('App\Entity\History');
     $user = $repository->findBy([]);
     if ($user == NULL)
     {
       return ($this->httpForbiden("Error database !"));
     }
     $data = $this->serializer->serialize($user, 'json');
     return $this->httpCreated($data);
   }
}
