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

    public function __construct(SerializerInterface $serializer){
      $this->serializer= $serializer;
    }

    /**
    * @Route("/users", methods={"GET"}, name="get_users")
    */

    public function AllUser()
    {
        $apikey = $request->headers->get('x-key');
        echo $apikey;
        exit(0);
      $repository = $this->getDoctrine()
                   ->getManager()
                   ->getRepository('App\Entity\User');
      $user = $repository->findBy([], ['points' => 'DESC']);
      if ($user == NULL)
      {
        $data = json_encode(array(
            "error"=> "Erreur !"
        ));
        $response =  new Response($data);
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
      }
      $data = $this->serializer->serialize($user, 'json');
      $response =  new Response($data);
      $response->headers->set('Content-Type', 'application/json');
      return $response;
    }


    /**
    * @Route("/user/{email}", methods={"GET"}, name="get_user")
    */

    public function OneUser($email)
    {
      $repository = $this->getDoctrine()
                   ->getManager()
                   ->getRepository('App\Entity\User');
      $user = $repository->findOneBy(array('email' => $email));
      if ($user == NULL)
      {
        $data = json_encode(array(
            "error"=> "Utilisateur inconnu !"
        ));
        $response =  new Response($data);
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
      }
      $data = $this->serializer->serialize($user, 'json');
      $response =  new Response($data);
      $response->headers->set('Content-Type', 'application/json');
      return $response;
    }


    /**
    * @Route("/user", methods={"POST"}, name="create_user")
    */
   public function CreateUser(Request $request, ValidatorInterface $validator)
   {
    $data = $request->getContent();
    $user =  $this->serializer->deserialize($data, 'App\Entity\User', 'json');
    $user->init();

    $errors = $validator->validate($user);
    if (count($errors) > 0) {
         $errorsString = $errors[0]->getMessage();
         $data = json_encode(array(
             "error"=> $errorsString
         ));
         $response =  new Response($data);
         $response->setStatusCode(Response::HTTP_BAD_REQUEST);
         $response->headers->set('Content-Type', 'application/json');
         return $response;
     }

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
