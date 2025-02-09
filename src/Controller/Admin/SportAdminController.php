<?php

namespace App\Controller\Admin;

use App\Entity\Sport;
use App\Form\SportType;
use App\Repository\SportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/sport')]
class SportAdminController extends AbstractController
{
    #[Route('/', name: 'sport_index', methods: ['GET'])]
    public function index(SportRepository $sportRepository): Response
    {
        return $this->render('sport/index.html.twig', [
            'sports' => $sportRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'sport_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sport = new Sport();
        $form = $this->createForm(SportType::class, $sport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sport);
            $entityManager->flush();

            return $this->redirectToRoute('sport_index');
        }

        return $this->render('sport/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'sport_show', methods: ['GET'])]
    public function show(Sport $sport): Response
    {
        return $this->render('sport/show.html.twig', [
            'sport' => $sport,
        ]);
    }

    #[Route('/{id}/edit', name: 'sport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sport $sport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SportType::class, $sport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('sport_index');
        }

        return $this->render('sport/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'sport_delete', methods: ['POST'])]
    public function delete(Request $request, Sport $sport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sport_index');
    }
}
