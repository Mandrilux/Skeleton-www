<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

  public function jsoncheck($json){
    $result = json_decode($json);
    if ($result == NULL)
    {
      return false;
    }
    return true;
  }
}
