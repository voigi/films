<?php

namespace App\Controller;
use App\Entity\Gender;

use App\Repository\GenderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// A Mettre pour serialiser le retour du service en json
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


/**
     * @Route("/gender")
     */
class GenderController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function api(GenderRepository $movieRepository):Response
    {
        
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $gender = $movieRepository->findAll();
        //var_dump($serializer->serialize($authors, 'json'));
        $gender = $serializer->serialize($gender, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return new Response($gender, 200, ['Content-Type' => 'application/json']);
    }
    /**
     * @Route("/{id}")
     */
    public function apiDetail(GenderRepository $genderRepository,$id):Response
    {
        
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $genders = $genderRepository->find($id);
        //var_dump($serializer->serialize($authors, 'json'));
        $genders = $serializer->serialize($genders, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return new Response($genders, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $gender = new Gender($data['name']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($gender);
        $entityManager->flush();
        
        return $this->json($data);
    }

   /**
     * @Route("/delete/{id}", methods={"DELETE"})
     */
    public function delete(GenderRepository $genderRepository, $id)
    {
        $gender = $genderRepository->find($id);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($gender);
        $entityManager->flush();       
        return $this->json("Genre supprime");
    }

    /**
     * @Route("/edit/{id}", methods={"PUT"})
     */
    public function edit(GenderRepository $genderRepository, Request $request, $id)
    {
        $data= json_decode($request->getContent(), true);
        $gender = $genderRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if (!$gender) {
            throw $this->createNotFoundException(
                'No genre found for id '.$id
            );
        }      
        $gender->setName($data['name']);
       
        $entityManager->flush();
        return $this->json("genre edite");
    }
}
