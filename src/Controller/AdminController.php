<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Label;
use App\Entity\Record;
use App\Form\ArtistFormType;
use App\Form\ConfirmDeletionFormType;
use App\Form\LabelFormType;
use App\Form\RecordFormType;
use App\Repository\ArtistRepository;
use App\Repository\LabelRepository;
use App\Repository\RecordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("", name="dashboard")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/artist", name="artist_list")
     */
    public function artistList(ArtistRepository $repository)
    {
        return $this->render('admin/artist_list.html.twig', [
            'artist_list' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/artist/new", name="artist_add")
     */
    public function artistAdd(Request $request, EntityManagerInterface $entityManager)
    {
        $artistForm = $this->createForm(ArtistFormType::class);
        $artistForm->handleRequest($request);

        if ($artistForm->isSubmitted() && $artistForm->isValid()) {
            $entityManager->persist($artistForm->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Nouvel artiste enregistré.');
            return $this->redirectToRoute('admin_artist_list');
        }

        return $this->render('admin/artist_form.html.twig', [
            'title' => 'Nouvel artiste',
            'artist_form' => $artistForm->createView(),
        ]);
    }

    /**
     * @Route("/artist/{id}/edit", name="artist_edit")
     */
    public function artistEdit(Artist $artist, Request $request, EntityManagerInterface $entityManager)
    {
        $artistForm = $this->createForm(ArtistFormType::class, $artist);
        $artistForm->handleRequest($request);

        if ($artistForm->isSubmitted() && $artistForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Artiste mis à jour.');
        }

        return $this->render('admin/artist_form.html.twig', [
            'title' => 'Artiste ' . $artist->getName(),
            'artist_form' => $artistForm->createView(),
        ]);
    }

    /**
     * @Route("/artist/{id}/delete", name="artist_delete")
     */
    public function artistDelete(Artist $artist, Request $request, EntityManagerInterface $entityManager)
    {
        $deleteForm = $this->createForm(ConfirmDeletionFormType::class);
        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $entityManager->remove($artist);
            $entityManager->flush();

            $this->addFlash('success', 'Artiste supprimé.');
            return $this->redirectToRoute('admin_artist_list');
        }

        return $this->render('admin/delete.html.twig', [
            'title' => 'Supprimer un artiste',
            'label' => 'Je confirme la suppression de l\'artiste ' . $artist->getName(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/record", name="record_list")
     */
    public function recordList(RecordRepository $repository)
    {
        return $this->render('admin/record_list.html.twig', [
            'record_list' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/record/new", name="record_add")
     */
    public function recordAdd(Request $request, EntityManagerInterface $entityManager)
    {
        $recordForm = $this->createForm(RecordFormType::class);
        $recordForm->handleRequest($request);

        if ($recordForm->isSubmitted() && $recordForm->isValid()) {
            $entityManager->persist($recordForm->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Nouvel album enregistré.');
            return $this->redirectToRoute('admin_record_list');
        }

        return $this->render('admin/record_form.html.twig', [
            'title' => 'Nouvel album',
            'record_form' => $recordForm->createView(),
        ]);
    }

    /**
     * @Route("/record/{id}/edit", name="record_edit")
     */
    public function recordEdit(Record $record, Request $request, EntityManagerInterface $entityManager)
    {
        $recordForm = $this->createForm(RecordFormType::class, $record);
        $recordForm->handleRequest($request);

        if ($recordForm->isSubmitted() && $recordForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Album mis à jour.');
        }

        return $this->render('admin/record_form.html.twig', [
            'title' => 'Album ' . $record->getTitle(),
            'record_form' => $recordForm->createView(),
        ]);
    }

    /**
     * @Route("/record/{id}/delete", name="record_delete")
     */
    public function recordDelete(Record $record, Request $request, EntityManagerInterface $entityManager)
    {
        $deleteForm = $this->createForm(ConfirmDeletionFormType::class);
        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $entityManager->remove($record);
            $entityManager->flush();

            $this->addFlash('success', 'Album supprimé.');
            return $this->redirectToRoute('admin_record_list');
        }

        return $this->render('admin/delete.html.twig', [
            'title' => 'Supprimer un album',
            'label' => 'Je confirme la suppression de l\'album ' . $record->getTitle(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/label", name="label_list")
     */
    public function labelList(LabelRepository $repository)
    {
        return $this->render('admin/label_list.html.twig', [
            'label_list' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/label/new", name="label_add")
     */
    public function labelAdd(Request $request, EntityManagerInterface $entityManager)
    {
        $labelForm = $this->createForm(LabelFormType::class);
        $labelForm->handleRequest($request);

        if ($labelForm->isSubmitted() && $labelForm->isValid()) {
            $entityManager->persist($labelForm->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Nouveau label enregistré.');
            return $this->redirectToRoute('admin_label_list');
        }

        return $this->render('admin/label_form.html.twig', [
            'title' => 'Nouveau label',
            'label_form' => $labelForm->createView(),
        ]);
    }

    /**
     * @Route("/label/{id}/edit", name="label_edit")
     */
    public function labelEdit(Label $label, Request $request, EntityManagerInterface $entityManager)
    {
        $labelForm = $this->createForm(LabelFormType::class, $label);
        $labelForm->handleRequest($request);

        if ($labelForm->isSubmitted() && $labelForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Label mis à jour.');
        }

        return $this->render('admin/label_form.html.twig', [
            'title' => 'Album ' . $label->getName(),
            'label_form' => $labelForm->createView(),
        ]);
    }

    /**
     * @Route("/label/{id}/delete", name="label_delete")
     */
    public function labelDelete(Label $label, Request $request, EntityManagerInterface $entityManager)
    {
        $deleteForm = $this->createForm(ConfirmDeletionFormType::class);
        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $entityManager->remove($label);
            $entityManager->flush();

            $this->addFlash('success', 'Label supprimé.');
            return $this->redirectToRoute('admin_label_list');
        }

        return $this->render('admin/delete.html.twig', [
            'title' => 'Supprimer un label',
            'label' => 'Je confirme la suppression du label ' . $label->getName(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }
}
