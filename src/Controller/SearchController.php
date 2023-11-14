<?php
// src/Controller/SearchController.php 

namespace App\Controller;

use App\Service\ElasticsearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{

    #[Route('/posts/search', name: 'search_route')]
    public function search(ElasticsearchService $elasticsearchService): JsonResponse
    {
        $index = 'all_posts'; // Replace with your Elasticsearch index
        $query = [
            // Your Elasticsearch query
            'query' => [
                'match' => [
                    'title' => 'search_term',
                    'description' => 'search_term'
                ],
            ],
        ];

        $result = $elasticsearchService->search($index, $query);

        // Process and return the Elasticsearch result as needed
        return $this->json($result);
    }
}


// return $this->render('post/search.html.twig', [
//     'posts' => $posts,
// ]);


        // For Partial Match
        // ->where('p.title LIKE :search')
        // ->orWhere('p.description LIKE :search')
        // ->setParameter('search', '%' . $search . '%')

        // For Exact Match
        // ->where('p.title = :search')
        // ->orWhere('p.description = :search')
        // ->setParameter('search', $search)

        // For just title
        // ->where('p.title = :search')
        // ->setParameter('search', $search)

        // Show the related posts that contain only related keyword
        // ->andWhere('p.title LIKE :search')
        // ->orWhere('p.description LIKE :search')
        // ->setParameter('search', $search)

        // ->getQuery()
        // ->getResult();

        // if (!$posts) {
        // $this->addFlash('message', 'No Posts Found');
        // return $this->redirectToRoute('all_posts');
        // return $this->render('post/noSearch.html.twig', [
        // 'posts' => $posts,
        // ]);
        // }

        // return $this->render('post/search.html.twig', [
        //     'posts' => $posts,
        // ]);
