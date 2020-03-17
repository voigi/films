<?php

namespace App\Controller;
use App\Entity\Actor;

use App\Repository\ActorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// A Mettre pour serialiser le retour du service en json
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
     * @Route("/actor")
     */
class ActorController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function api(ActorRepository $movieRepository):Response
    {
        
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $actors = $movieRepository->findAll();
        //var_dump($serializer->serialize($authors, 'json'));
        $actors = $serializer->serialize($actors, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return new Response($actors, 200, ['Content-Type' => 'application/json']);
    }
       /**
     * @Route("/{id}")
     */
    public function apiDetail(ActorRepository $actorRepository,$id):Response
    {
        
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $actors = $actorRepository->find($id);
        //var_dump($serializer->serialize($authors, 'json'));
        $actors = $serializer->serialize($actors, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return new Response($actors, 200, ['Content-Type' => 'application/json']);
    }
}
