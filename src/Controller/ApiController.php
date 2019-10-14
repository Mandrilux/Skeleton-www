<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\History;

class ApiController extends AbstractController
{

  public function badRequest($error)
  {
    $data = json_encode(array(
        "error"=> $error,
        "code" => Response::HTTP_BAD_REQUEST
    ));
    $response =  new Response($data);
    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function httpCreated($data){
    $response =  new Response($data);
    $response->setStatusCode(Response::HTTP_CREATED);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function httpForbiden($error){
    $data = json_encode(array(
        "error"=> $error,
        "code" => Response::HTTP_FORBIDDEN
    ));
    $response =  new Response($data);
    $response->setStatusCode(Response::HTTP_FORBIDDEN);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  public function saveHistory($request, $user)
  {

    $em = $this->getDoctrine()->getManager();
    $routename = $request->server->get("PATH_INFO");
    $routemethode = $request->server->get("REQUEST_METHOD");
    $ip = $request->server->get("REMOTE_ADDR");
    $history = new History($routename, $routemethode, $user, $ip);
    $flag = $em->getRepository("App\Entity\History")->findOneBy(['name' => $routename, 'method' => $routemethode, 'user' => $user]);
    if ($flag == NULL)
    {
      $user->updatePoints(50);
    }
    $user->setLastRequest(New \datetime());
    $em->persist($user);
    $em->persist($history);
    $em->flush();
  }

  public function jsoncheck($json){
    $result = json_decode($json);
    if ($result == NULL)
    {
      return false;
    }
    return true;
  }

  public function LoginEpitech($user, $passwd){
        $passwd = hash("sha512", $passwd);
        $passwd = hash_hmac("sha512", $user, $passwd);
        $data = array("user" => $user, "signature" => $passwd);
        $data = json_encode($data);
        $curl = curl_init('https://blih.epitech.eu/whoami');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data),
                'User-Agent: EpiApi 0.1')
        );
        $result = curl_exec($curl);
        $result = json_decode($result, true);
        if (isset($result['error'])){
          return false;
        }
        else{
          return true;
        }
  }
}
