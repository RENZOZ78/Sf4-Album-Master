<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Label;
use App\Entity\Note;
use App\Entity\Record;
use App\Form\ConfirmDeletionFormType;
use App\Form\NoteFormType;
use App\Repository\ArtistRepository;
use App\Repository\NoteRepository;
use App\Repository\RecordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecordController extends AbstractController
{
    /**
     * Liste des artistes
     * @Route("/artist", name="artist_list")
     */
    public function index(ArtistRepository $repository)
    {
        return $this->render('record/artist_list.html.twig', [
            'artist_list' => $repository->findAll(),
        ]);
    }

    /**
     * Page d'un artiste
     * @Route("/artist/{id}", name="artist_page")
     */
    public function artistPage(Artist $artist)
    {
        return $this->render('record/artist_page.html.twig', [
            'artist' => $artist
        ]);
    }

    /**
     * Page d'un album
     * @Route("/record/{id}", name="record_page")
     */
    public function recordPage(Record $record, NoteRepository $repository, Request $request, EntityManagerInterface $entityManager)
    {
        // Afficher le formulaire uniquement si l'utilisateur est connecté
        if ($this->getUser()) {

            // Rechercher une note déjà existante pour la modifier
            $note = $repository->findOneBy([
                'author' => $this->getUser(),
                'record' => $record,
            ]);

            // Si la note n'existe pas, on en crée une nouvelle
            $note = $note ?? (new Note())
                ->setAuthor($this->getUser())
                ->setRecord($record)
                ->setCreatedAt(new \DateTime());

            // Traitement du formulaire
            $noteForm = $this->createForm(NoteFormType::class, $note);
            $noteForm->handleRequest($request);

            if ($noteForm->isSubmitted() && $noteForm->isValid()) {
                $entityManager->persist($note);
                $entityManager->flush();

                $this->addFlash('success', 'Votre note a bien été enregistrée');
                return $this->redirectToRoute('record_page', ['id' => $record->getId()]);
            }
        }

        return $this->render('record/record_page.html.twig', [
            'record' => $record,
            'note_form' => isset($noteForm) ? $noteForm->createView() : null,
        ]);
    }

    /**
     * Nouveaux albums
     * @Route("/news", name="record_news")
     */
    public function recordNews(RecordRepository $repository)
    {
        return $this->render('record/record_news.html.twig', [
            'record_news' => $repository->findNews(),
        ]);
    }

    /**
     * Page d'un label
     * @Route("/label/{id}", name="label_page")
     */
    public function labelPage(Label $label)
    {
        return $this->render('record/label_page.html.twig', [
            'label' => $label
        ]);
    }

    /**
     * Page de suppression d'une note
     * @Route("/note/{id}/delete", name="note_delete")
     * @IsGranted("NOTE_DELETE", subject="note")
     */
    public function deleteNote(Note $note, Request $request, EntityManagerInterface $entityManager)
    {
        $deleteForm = $this->createForm(ConfirmDeletionFormType::class);
        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $entityManager->remove($note);
            $entityManager->flush();

            $this->addFlash('info', 'La note a été supprimée');
            return $this->redirectToRoute('record_page', [
                'id' => $note->getRecord()->getId(),
            ]);
        }

        return $this->render('record/note_delete.html.twig', [
            'note' => $note,
            'delete_form' => $deleteForm->createView(),
        ]);
    }
}
