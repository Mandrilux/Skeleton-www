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

class KeyController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer){
      $this->serializer= $serializer;
    }

    /**
    * @Route("/user/key", methods={"POST"}, name="set_key")
    */

    public function setKey(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $username = $request->headers->get('php-auth-user');
      $aToken = $request->headers->get('php-auth-pw');

      $user = $em->getRepository("App\Entity\User")->findOneBy(['email' => $username, 'password' => $aToken]);
      if ($user == NULL)
      {
        $data = json_encode(array(
            "error"=> "BAD CREDENTIAL"
        ));
        $response =  new Response($data);
        $response->setStatusCode(Response::HTTP_FORBIDDEN );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
      }
      else{
        $user->genKey();
        $em->persist($user);
        $em->flush();

        $data = json_encode(array(
            "key"=> $user->getApikey()
        ));
        $response =  new Response($data);
        $response->setStatusCode(Response::HTTP_OK );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
      }
    }

    /**
    * @Route("/user/key", methods={"get"}, name="get_key")
    */

    public function getKey(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $username = $request->headers->get('php-auth-user');
      $aToken = $request->headers->get('php-auth-pw');

      $user = $em->getRepository("App\Entity\User")->findOneBy(['email' => $username, 'password' => $aToken]);
      if ($user == NULL)
      {
        $data = json_encode(array(
            "error"=> "BAD CREDENTIAL"
        ));
        $response =  new Response($data);
        $response->setStatusCode(Response::HTTP_FORBIDDEN );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
      }
      else{
        $data = json_encode(array(
            "key"=> $user->getApikey()
        ));
        $response =  new Response($data);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
      }
    }
}
