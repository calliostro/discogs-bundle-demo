<?php

namespace App\Controller;

use Discogs\DiscogsClient;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index", methods={"GET"})
     */
    public function index(DiscogsClient $discogs): Response
    {
        try {
            $identity = $discogs->getOAuthIdentity();
        } catch (Exception $e) {
            return $this->redirectToRoute('hwi_oauth_connect');
        }

        return $this->render('default.html.twig', ['identity' => $identity]);
    }
}
