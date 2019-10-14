<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class KeyController extends ApiController
{

    /**
    * @Route("/user/key", methods={"PUT"}, name="set_key")
    */

    public function setKey(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $username = $request->headers->get('php-auth-user');
      $aToken = $request->headers->get('php-auth-pw');
      if ($this->LoginEpitech($username, $aToken) == false){
        return ($this->httpForbiden("Bad credential !"));
      }
      $user = $em->getRepository("App\Entity\User")->findOneBy(['email' => $username]);
      $user->genKey();
      $em->persist($user);
      $em->flush();
      $data = json_encode(array(
            "key"=> $user->getApikey()
      ));
      $this->saveHistory($request, $user);
      return $this->httpCreated($data);
    }

    /**
    * @Route("/user/key", methods={"get"}, name="get_key")
    */

    public function getKey(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $username = $request->headers->get('php-auth-user');
      $aToken = $request->headers->get('php-auth-pw');
      if ($this->LoginEpitech($username, $aToken) == false){
        return ($this->httpForbiden("Bad credential !"));
      }
      $user = $em->getRepository("App\Entity\User")->findOneBy(['email' => $username]);
        $data = json_encode(array(
            "key"=> $user->getApikey()
      ));
      $this->saveHistory($request, $user);
      return $this->httpCreated($data);
    }
}
