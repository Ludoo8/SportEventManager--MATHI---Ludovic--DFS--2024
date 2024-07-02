<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/participants')]
class ParticipantController extends AbstractController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'participant_index', methods: ['GET'])]
    public function index(ParticipantRepository $participantRepository): JsonResponse
    {
        $participants = $participantRepository->findAll();
        $data = $this->serializer->serialize($participants, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/', name: 'participant_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $participant = new Participant();
        $participant->setName($data['name']);
        $participant->setAge($data['age']);

        $this->entityManager->persist($participant);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($participant, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'participant_show', methods: ['GET'])]
    public function show(Participant $participant): JsonResponse
    {
        $data = $this->serializer->serialize($participant, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'participant_edit', methods: ['PUT'])]
    public function edit(Request $request, Participant $participant): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $participant->setName($data['name']);
        $participant->setAge($data['age']);

        $this->entityManager->flush();

        $data = $this->serializer->serialize($participant, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'participant_delete', methods: ['DELETE'])]
    public function delete(Participant $participant): JsonResponse
    {
        $this->entityManager->remove($participant);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
