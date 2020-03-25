<?php

namespace App\Controller;
use App\Entity\Movies;

use App\Repository\MoviesRepository;
use App\Repository\GenderRepository;
use App\Repository\ActorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        $movies = $movieRepository->findBy([],['year'=>'DESC']); // Modification de la requête pour avoir des dates dans l'order décroissantes
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
     * 
     */
    public function apiNew(Request $request, GenderRepository $genderRepository, ActorRepository $actorRepository)
    {
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $data = json_decode($request->getContent(), true);

       
       
        // genre est ici la cle étrangere de la table movie
        $genre = $genderRepository->find($data['gender_id']);

        $movie = new Movies($data['title'], $data['description'], $data['year'], $data['picture'], $data['note'], $genre);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($movie);
        $entityManager->flush();

    if (is_array($data['actor']) || is_object($data['actor']))
    {
     foreach($data['actor'] as $actor){
         $actor = $actorRepository->find($actor);
         $movies->addActor($actor);
         $entityManager->persist($movies);
     }
    }
     $entityManager->flush();

        return $this->json($data);
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
     * @Route("/delete/{id}", methods={"DELETE"})
     */
    public function delete(MoviesRepository $movieRepository, $id)
    {
        $movie = $movieRepository->find($id);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($movie);
        $entityManager->flush();

        return $this->json("Movie supprimé");
    }

     /**
     * @Route("/edit/{id}", methods={"PUT"})
     */
    public function edit(MoviesRepository $movieRepository,GenderRepository $genderRepository, Request $request, $id)
    {
        $data= json_decode($request->getContent(), true);
        $movie = $movieRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if (!$movie) {
            throw $this->createNotFoundException(
                'No movie found for id '.$id
            );
        } 
         // on  va chercher pour l'element date une nouvelle datetime
         $time = new \DateTime($data['year']);
         // genre est ici la cle etrangere de la table movie
         $genre = $genderRepository->find($data['gender_id']);
        
        $movie->setGender($genre);
        $movie->setTitle($data['title']);
        $movie->setDescription($data['description']);
        $movie->setYear($time);
        $movie->setTitle($data['title']);
        $movie->setPicture($data['picture']);
        $movie->setNote($data['note']);

        $entityManager->flush();
        return $this->json("Movie edite");
    }


}
