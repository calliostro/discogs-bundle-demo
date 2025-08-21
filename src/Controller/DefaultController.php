<?php

namespace App\Controller;

use Discogs\DiscogsClient;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default_index', methods: ['GET'])]
    public function index(DiscogsClient $discogs): Response
    {
        try {
            $identity = $discogs->getOAuthIdentity();
        } catch (Exception $e) {
            return $this->redirectToRoute('hwi_oauth_service_redirect', ['service' => 'discogs']);
        }

        return $this->render('default.html.twig', ['identity' => $identity]);
    }

    #[Route('/artist/{id}', name: 'artist_detail', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function artistDetail(DiscogsClient $discogs, int $id): Response
    {
        try {
            $artist = $discogs->getArtist(['id' => $id]);
            $artistReleases = $discogs->getArtistReleases(['id' => $id, 'per_page' => 10]);

            return $this->render('artist_detail.html.twig', [
                'artist' => $artist,
                'releases' => $artistReleases
            ]);
        } catch (Exception $e) {
            $this->addFlash('error', 'Artist could not be loaded: ' . $e->getMessage());
            return $this->redirectToRoute('default_index');
        }
    }

    #[Route('/release/{id}', name: 'release_detail', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function releaseDetail(DiscogsClient $discogs, int $id): Response
    {
        try {
            $release = $discogs->getRelease(['id' => $id]);

            return $this->render('release_detail.html.twig', [
                'release' => $release
            ]);
        } catch (Exception $e) {
            $this->addFlash('error', 'Release could not be loaded: ' . $e->getMessage());
            return $this->redirectToRoute('default_index');
        }
    }

    #[Route('/search', name: 'search', methods: ['GET', 'POST'])]
    public function search(DiscogsClient $discogs, Request $request): Response
    {
        $searchResults = null;
        $searchQuery = '';
        $searchType = 'all';

        if ($request->isMethod('POST') || $request->query->get('q')) {
            $searchQuery = $request->request->get('q') ?? $request->query->get('q', '');
            $searchType = $request->request->get('type') ?? $request->query->get('type', 'all');

            if (!empty($searchQuery)) {
                try {
                    $searchParams = ['q' => $searchQuery, 'per_page' => 20];
                    if ($searchType !== 'all') {
                        $searchParams['type'] = $searchType;
                    }

                    $searchResults = $discogs->search($searchParams);
                } catch (Exception $e) {
                    $this->addFlash('error', 'Search failed: ' . $e->getMessage());
                }
            }
        }

        return $this->render('search.html.twig', [
            'searchResults' => $searchResults,
            'searchQuery' => $searchQuery,
            'searchType' => $searchType
        ]);
    }

    #[Route('/collection', name: 'user_collection', methods: ['GET'])]
    public function userCollection(Request $request, DiscogsClient $discogs): Response
    {
        try {
            $identity = $discogs->getOAuthIdentity();
            $folders = $discogs->getCollectionFolders(['username' => $identity['username']]);

            // Get folder_id from query parameter, default to first folder
            $folderId = $request->query->get('folder');
            $selectedFolder = null;
            $collectionItems = null;

            if (!empty($folders['folders'])) {
                // If folder_id is specified, find that folder, otherwise use first folder
                if ($folderId) {
                    foreach ($folders['folders'] as $folder) {
                        if ($folder['id'] == $folderId) {
                            $selectedFolder = $folder;
                            break;
                        }
                    }
                }

                // If no specific folder found or no folder_id specified, use first folder
                if (!$selectedFolder) {
                    $selectedFolder = $folders['folders'][0];
                }

                // Get page parameter for pagination
                $page = max(1, (int) $request->query->get('page', 1));

                $collectionItems = $discogs->getCollectionItemsByFolder([
                    'username' => $identity['username'],
                    'folder_id' => $selectedFolder['id'],
                    'per_page' => 20,
                    'page' => $page
                ]);
            }

            return $this->render('collection.html.twig', [
                'folders' => $folders,
                'collectionItems' => $collectionItems,
                'selectedFolder' => $selectedFolder,
                'username' => $identity['username']
            ]);
        } catch (Exception $e) {
            $this->addFlash('error', 'Collection could not be loaded: ' . $e->getMessage());
            return $this->redirectToRoute('default_index');
        }
    }

    #[Route('/wantlist', name: 'user_wantlist', methods: ['GET'])]
    public function userWantlist(DiscogsClient $discogs): Response
    {
        try {
            $identity = $discogs->getOAuthIdentity();
            $wantlist = $discogs->getWantlist([
                'username' => $identity['username'],
                'per_page' => 20
            ]);

            return $this->render('wantlist.html.twig', [
                'wantlist' => $wantlist,
                'username' => $identity['username']
            ]);
        } catch (Exception $e) {
            $this->addFlash('error', 'Wantlist could not be loaded: ' . $e->getMessage());
            return $this->redirectToRoute('default_index');
        }
    }

    #[Route('/profile', name: 'user_profile', methods: ['GET'])]
    public function userProfile(DiscogsClient $discogs): Response
    {
        try {
            $identity = $discogs->getOAuthIdentity();
            $profile = $discogs->getProfile(['username' => $identity['username']]);

            return $this->render('profile.html.twig', [
                'profile' => $profile,
                'identity' => $identity
            ]);
        } catch (Exception $e) {
            $this->addFlash('error', 'Profile could not be loaded: ' . $e->getMessage());
            return $this->redirectToRoute('default_index');
        }
    }

    #[Route('/random', name: 'random_releases', methods: ['GET'])]
    public function randomReleases(DiscogsClient $discogs): Response
    {
        try {
            // Search for some popular artists to get interesting results
            $artists = ['Pink Floyd', 'The Beatles', 'Led Zeppelin', 'Bob Dylan', 'David Bowie'];
            $randomArtist = $artists[array_rand($artists)];

            $searchResults = $discogs->search([
                'q' => $randomArtist,
                'type' => 'release',
                'per_page' => 10
            ]);

            return $this->render('random_releases.html.twig', [
                'releases' => $searchResults,
                'artist' => $randomArtist
            ]);
        } catch (Exception $e) {
            $this->addFlash('error', 'Random releases could not be loaded: ' . $e->getMessage());
            return $this->redirectToRoute('default_index');
        }
    }
}
