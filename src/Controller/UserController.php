<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    private $serializer;
    private $validator;

    public function __construct(SerializerInterface $serializer){
      $this->serializer= $serializer;
    }

    /**
    * @Route("/user", methods={"GET"}, name="get_user")
    */

    public function OneUser()
    {
      echo "ok";
      exit(0);
    /*  $data = $this->serializer->serialize($league, 'json');
      $response =  new Response($data);
      $response->headers->set('Content-Type', 'application/json');
      return $response;*/
    }


    /**
    * @Route("/user", methods={"POST"}, name="create_user")
    */
   public function CreateUser(Request $request, ValidatorInterface $validator)
   {
    $data = $request->getContent();
    $user =  $this->serializer->deserialize($data, 'App\Entity\User', 'json');

    $acceptedDomains = array('epitech.eu');

    if(!in_array(substr($user->getEmail(), strrpos($user->getEmail(), '@') + 1), $acceptedDomains))
    {
      $data = json_encode(array(
          "error"=> "L'email doit etre un email Epitech (prenom.nom@epitech.eu)"
      ));
      $response =  new Response($data);
      $response->setStatusCode(Response::HTTP_BAD_REQUEST);
      $response->headers->set('Content-Type', 'application/json');
      return $response;
    }

    if (strlen($user->getEmail())  < strlen("epitech.eu") + 4)
    {
      $data = json_encode(array(
          "error"=> "Email invalide (prenom.nom@epitech.eu)"
      ));
      $response =  new Response($data);
      $response->setStatusCode(Response::HTTP_BAD_REQUEST);
      $response->headers->set('Content-Type', 'application/json');
      return $response;
    }

    $user->init();

    $errors = $validator->validate($user);
    if (count($errors) > 0) {
         /*
          * Uses a __toString method on the $errors variable which is a
          * ConstraintViolationList object. This gives us a nice string
          * for debugging.
          */
         $errorsString = $errors[0]->getMessage();
         $data = json_encode(array(
             "error"=> $errorsString
         ));
         $response =  new Response($data);
         $response->setStatusCode(Response::HTTP_BAD_REQUEST);
         $response->headers->set('Content-Type', 'application/json');
         return $response;
     }

    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();
    $data = json_encode(array(
        "email"=> $user->getEmail(),
        "key"=>$user->getApikey()
    ));
    $response =  new Response($data);
    $response->setStatusCode(Response::HTTP_CREATED);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
