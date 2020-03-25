<?php

namespace App\Controller;
use App\Entity\Actor;

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
     * @Route("/actor")
     */
class ActorController extends AbstractController
{
    /**
     * @Route("/")
     *
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
     * @Route("/feminin")
     *
     */
    //fonction pour la liste des actrices
    public function feminin(ActorRepository $movieRepository):Response
    {
        
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $actors = $movieRepository->findBy(['gender' => 'F']);
        //var_dump($serializer->serialize($authors, 'json'));
        $actors = $serializer->serialize($actors, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return new Response($actors, 200, ['Content-Type' => 'application/json']);
    }
    /**
     * @Route("/masculin")
     *
     */
    //fonction pour la liste des acteurs
    public function masculin(ActorRepository $movieRepository):Response
    {
        
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $actors = $movieRepository->findBy(['gender' => 'M']);
        //var_dump($serializer->serialize($authors, 'json'));
        $actors = $serializer->serialize($actors, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return new Response($actors, 200, ['Content-Type' => 'application/json']);
    }

    
    /**
     * @Route("/new", methods={"POST"})
     * 
     */
    public function apiNew(Request $request):Response
    {
        $data = json_decode($request->getContent(), true);
        $birth = new \Datetime($data['birth']);

        $actor = new Actor($data['name'],$data['firstname'],$birth,$data['gender'],$data['nationality']);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($actor);
        $entityManager->flush();
        return $this->json($data);
       // return var_dump($actor);
       
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

    /**
     * @Route("/delete/{id}", methods={"DELETE"})
     */
        public function delete(ActorRepository $actorRepository, $id)
        {
            $actor = $actorRepository->find($id);
                  
         
       
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($actor);
        $entityManager->flush();
        return $this->json("Actor supprimé");
    }

    /**
     * @Route("/edit/{id}", methods={"PUT"})
     */
    public function edit(ActorRepository $actorRepository, Request $request, $id)
    {
        $data= json_decode($request->getContent(), true);
        $actor = $actorRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if (!$actor) {
            throw $this->createNotFoundException(
                'No actor found for id '.$id
            );
        }      
        $birth= new \Datetime($data['birth']);

        $actor->setName($data['name']);
        $actor->setFirstname($data['firstname']);
        $actor->setBirth($birth);
        $actor->setGender($data['gender']);
        $actor->setNationality($data['nationality']);
    
        $entityManager->flush();
        return $this->json("Actor edite");
    }
    



}
