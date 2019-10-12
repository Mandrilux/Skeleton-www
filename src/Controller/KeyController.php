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
    * @Route("/user/key", methods={"POST"}, name="set_key")
    */

    public function setKey(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $username = $request->headers->get('php-auth-user');
      $aToken = $request->headers->get('php-auth-pw');
      $user = $em->getRepository("App\Entity\User")->findOneBy(['email' => $username, 'password' => $aToken]);
      if ($user == NULL){
        return ($this->httpForbiden("Bad credential !"));
      }
      $user->genKey();
      $em->persist($user);
      $em->flush();
      $data = json_encode(array(
            "key"=> $user->getApikey()
      ));
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

      $user = $em->getRepository("App\Entity\User")->findOneBy(['email' => $username, 'password' => $aToken]);
      if ($user == NULL)
      {
        return ($this->httpForbiden("Bad credential !"));
      }
        $data = json_encode(array(
            "key"=> $user->getApikey()
      ));
      return $this->httpCreated($data);
    }
}
