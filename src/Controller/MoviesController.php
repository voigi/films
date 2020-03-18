<?php

namespace App\Controller;
use App\Entity\Movies;

use App\Repository\MoviesRepository;
use App\Repository\GenderRepository;
use App\Repository\ActorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     /**
     * @Route("/new",methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function apiNew(Request $request, GenderRepository $genderRepository, ActorRepository $actorRepository)
    {
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $data = json_decode($request->getContent(), true);

        // on  va chercher pour l'element date une nouvelle datetime
        $time = new \DateTime($data['year']);
        // genre est ici la cle etrangere de la table movie
        $genre = $genderRepository->find($data['gender_id']);

        $movie = new Movie($data['title'], $data['description'], $time, $data['picture'], $data['note'], $genre);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($movie);
        $entityManager->flush();

     

        return $this->json($data);
    }

}
