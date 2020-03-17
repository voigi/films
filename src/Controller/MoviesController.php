<?php

namespace App\Controller;
use App\Entity\Movies;

use App\Repository\MoviesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// A Mettre pour serialiser le retour du service en json
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
     * @Route("/movie")
     */
class MoviesController extends AbstractController
{
    /**
     * @Route("/",methods={"GET"})
     */
    public function api(MoviesRepository $movieRepository):Response
    {
        
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $movies = $movieRepository->findAll();
        //var_dump($serializer->serialize($authors, 'json'));
        $movies = $serializer->serialize($movies, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return new Response($movies, 200, ['Content-Type' => 'application/json']);
    }


    /**
     * @Route("/{id}")
     */
    public function apiDetail(MoviesRepository $movieRepository,$id):Response
    {
        
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $movies = $movieRepository->find($id);
        //var_dump($serializer->serialize($authors, 'json'));
        $movies = $serializer->serialize($movies, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return new Response($movies, 200, ['Content-Type' => 'application/json']);
    }
}
