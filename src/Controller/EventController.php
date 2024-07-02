<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/events')]
class EventController extends AbstractController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): JsonResponse
    {
        $events = $eventRepository->findAll();
        $data = $this->serializer->serialize($events, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/', name: 'event_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $event = new Event();
        $event->setName($data['name']);
        $event->setDate(new \DateTime($data['date']));

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($event, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'event_show', methods: ['GET'])]
    public function show(Event $event): JsonResponse
    {
        $data = $this->serializer->serialize($event, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'event_edit', methods: ['PUT'])]
    public function edit(Request $request, Event $event): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $event->setName($data['name']);
        $event->setDate(new \DateTime($data['date']));

        $this->entityManager->flush();

        $data = $this->serializer->serialize($event, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'event_delete', methods: ['DELETE'])]
    public function delete(Event $event): JsonResponse
    {
        $this->entityManager->remove($event);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
