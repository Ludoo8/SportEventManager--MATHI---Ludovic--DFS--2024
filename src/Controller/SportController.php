<?php

namespace App\Controller;

use App\Entity\Sport;
use App\Repository\SportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/sports')]
class SportController extends AbstractController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'sport_index', methods: ['GET'])]
    public function index(SportRepository $sportRepository): JsonResponse
    {
        $sports = $sportRepository->findAll();
        $data = $this->serializer->serialize($sports, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/', name: 'sport_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $sport = new Sport();
        $sport->setName($data['name']);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($sport, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'sport_show', methods: ['GET'])]
    public function show(Sport $sport): JsonResponse
    {
        $data = $this->serializer->serialize($sport, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'sport_edit', methods: ['PUT'])]
    public function edit(Request $request, Sport $sport): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $sport->setName($data['name']);

        $this->entityManager->flush();

        $data = $this->serializer->serialize($sport, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'sport_delete', methods: ['DELETE'])]
    public function delete(Sport $sport): JsonResponse
    {
        $this->entityManager->remove($sport);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
