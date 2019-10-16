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
use JMS\Serializer\SerializationContext;

class UserController extends ApiController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer){
      $this->serializer= $serializer;
    }


    /**
    * @Route("/user", methods={"DELETE"}, name="delete_user")
    */

      public function DeleteUser(Request $request)
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
      $em = $this->getDoctrine()->getManager();
      $em->remove($user);
      $em->flush();
      return $this->httpDelete("User deleted !");
    }

    /**
    * @Route("/user", methods={"PUT"}, name="set_nickname")
    */

    public function NicknameUser(Request $request)
    {
      $data = $request->getContent();
      if (!$this->jsoncheck($data)){
        return ($this->badRequest("Json format is invalid"));
      }
      $datadecode =json_decode($data, true);
      if (!isset($datadecode["nickname"]))
      {
          return ($this->badRequest("Missing parameter nickname"));
      }
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
      $user->setNickname($datadecode["nickname"]);
      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->flush();
      $data = $this->serializer->serialize($user, 'json' , SerializationContext::create()->setGroups(array('nickname')));
      $this->saveHistory($request, $user);
      return $this->httpOk($data);
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

        try {
          $repository = $this->getDoctrine()
                       ->getManager()
                       ->getRepository('App\Entity\User');
          $users = $repository->findBy([], ['points' => 'DESC']);
          if ($users == NULL)
          {
            return ($this->httpForbiden("Error database !"));
          }
        }
        catch(DBALException $e){
          return ($this->badRequest($errorMessage = $e->getMessage()));
        //  $errorMessage = $e->getMessage();
        }
        catch(\Exception $e){
            return ($this->badRequest($e->getMessage()));
        }
      $data = $this->serializer->serialize($users, 'json' , SerializationContext::create()->setGroups(array('listUser')));
      //$data = $this->serializer->serialize($users, 'json');
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
      return $this->httpOk($data);
    }

    /**
    * @Route("/user", methods={"POST"}, name="create_user")
    */

   public function CreateUser(Request $request, ValidatorInterface $validator)
   {
  /*  $data = $request->getContent();
    if (!$this->jsoncheck($data)){
      return ($this->badRequest("Json format is invalid"));
    }
    $datadecode =json_decode($data, true);
    if (!isset($datadecode["email"])|| !isset($datadecode["password"]))
    {
        return ($this->badRequest("Missing parameters"));
    }*/

    $email = $request->headers->get('php-auth-user');
    $password = $request->headers->get('php-auth-pw');
    /*$email = $datadecode["email"];
    $password = $datadecode["password"];*/
    $user = new User($email);
    $errors = $validator->validate($user);
    if (count($errors) > 0) {
        return ($this->badRequest($errors[0]->getMessage()));
     }
    if ($this->LoginEpitech($email, $password) == false){
      return ($this->httpForbiden("Bad credential !"));
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
