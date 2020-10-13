<?php

namespace App\Controller;

use App\Repository\RecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(RecordRepository $repository)
    {
        return $this->render('home/index.html.twig', [
            'top_records' => $repository->topTen(),
        ]);
    }
}
