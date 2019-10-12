<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializerInterface;

class UserController extends ApiController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer){
      $this->serializer= $serializer;
    }

    /**
    * @Route("/users", methods={"GET"}, name="get_users")
    */

    public function AllUser(Request $request)
    {
        $apikey = $request->headers->get('x-key');
        if ($apikey == NULL | $apikey != "U3BPHB8iL96RZy4xdk26viTh4Mc8ebt2rZ454GM4V8hLjkc2UdbAje6wiH6y5u93apT8jVJF9PAQ5fKmw3kM94bnVY2G44Ph4Be7vb7UA6A3K7JM5jJL3f7g8Gq65n9U")
        {
            return ($this->httpForbiden("Bad credential !"));
        }
      $repository = $this->getDoctrine()
                   ->getManager()
                   ->getRepository('App\Entity\User');
      $user = $repository->findBy([], ['points' => 'DESC']);
      if ($user == NULL)
      {
        return ($this->httpForbiden("Error database !"));
      }
      $data = $this->serializer->serialize($user, 'json');
      return $this->httpCreated($data);
    }


    /**
    * @Route("/user", methods={"GET"}, name="get_user")
    */

    public function OneUser(Request $request)
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
      $data = $this->serializer->serialize($user, 'json');
      $this->saveHistory($request, $user);
      return $this->httpCreated($data);
    }

    /**
    * @Route("/user", methods={"POST"}, name="create_user")
    */

   public function CreateUser(Request $request, ValidatorInterface $validator)
   {
    $data = $request->getContent();
    if (!$this->jsoncheck($data)){
      return ($this->badRequest("Json format is invalid"));
    }
    $user =  $this->serializer->deserialize($data, 'App\Entity\User', 'json');
    $user->init();
    $errors = $validator->validate($user);
    if (count($errors) > 0) {
        return ($this->badRequest($errors[0]->getMessage()));
     }
    $acceptedDomains = array('epitech.eu');
    if(!in_array(substr($user->getEmail(), strrpos($user->getEmail(), '@') + 1), $acceptedDomains)){
      return ($this->badRequest("Email must be an Epitech email (prenom.nom@epitech.eu)"));
    }
    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();
    $data = json_encode(array(
        "email"=> $user->getEmail(),
        "key"=>$user->getApikey(),
    ));
    $this->saveHistory($request, $user);
    return $this->httpCreated($data);
  }
}
