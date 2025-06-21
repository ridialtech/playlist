<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/playlist')]
class PlaylistController extends AbstractController
{
    #[Route('/', name: 'playlist_index')]
    #[IsGranted('ROLE_USER')]
    public function index(PlaylistRepository $playlistRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $playlists = $playlistRepository->findAll();
        } else {
            $playlists = $playlistRepository->findBy(['user' => $this->getUser()]);
        }

        return $this->render('playlist/index.html.twig', [
            'playlists' => $playlists,
        ]);
    }

    #[Route('/new', name: 'playlist_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlist->setUser($this->getUser());
            $em->persist($playlist);
            $em->flush();

            return $this->redirectToRoute('playlist_index');
        }

        return $this->render('playlist/new.html.twig', [
            'form' => $form,
        ]);
    }
}

