<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;

class HistoryController extends ApiController
{
  private $serializer;

  public function __construct(SerializerInterface $serializer){
    $this->serializer= $serializer;
  }



  /**
  * @Route("/request", methods={"GET"}, name="get_all_request")
  */

 public function getAllRequest(Request $request)
 {
  /* $apikey = $request->headers->get('x-key');
   if ($apikey == NULL)
   {
     return ($this->badRequest("Missing key !"));
   }
   $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('App\Entity\User');
   $user = $repository->findOneBy(array('apikey' => $apikey));
   if ($user == NULL){
     return ($this->httpForbiden("Bad credential !"));
   }*/
   $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('App\Entity\History');
   $requests = $repository->findBy(array(), array('create_at'=>'desc'), 7, 0);
   if ($requests == NULL)
   {
     return ($this->badRequest("No request found !"));
   }
    //  $this->saveHistory($request, $user);
     $data = $this->serializer->serialize($requests, 'json' , SerializationContext::create()->setGroups(array('getRequest')));

   //$data = $this->serializer->serialize($user, 'json');
   return $this->httpCreated($data);
 }



    /**
    * @Route("/user/request", methods={"GET"}, name="get_request")
    */

   public function getUserRequest(Request $request)
   {
     $apikey = $request->headers->get('x-key');
     if ($apikey == NULL)
     {
       return ($this->badRequest("Missing key !"));
     }
     $repository = $this->getDoctrine()
                  ->getManager()
                  ->getRepository('App\Entity\User');
     $user = $repository->findOneBy(array('apikey' => $apikey));
     if ($user == NULL){
       return ($this->httpForbiden("Bad credential !"));
     }
     $repository = $this->getDoctrine()
                  ->getManager()
                  ->getRepository('App\Entity\History');
     $requests = $repository->findBy(array("user" => $user), array('create_at'=>'desc'));
     if ($requests == NULL)
     {
       return ($this->badRequest("Error database !"));
     }
        $this->saveHistory($request, $user);
       $data = $this->serializer->serialize($requests, 'json' , SerializationContext::create()->setGroups(array('getRequest')));

     //$data = $this->serializer->serialize($user, 'json');
     return $this->httpCreated($data);
   }
}
