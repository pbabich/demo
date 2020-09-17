<?php

namespace App\Controller;

use App\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class NewsController extends AbstractController
{
    /**
     * @Route("/news", name="news")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        // Retrieve the entity manager of Doctrine
        $em = $this->getDoctrine()->getManager();

        // Get some repository of data, in our case we have an News entity
        $newsRepository = $em->getRepository(News::class);

        // Find all the data on the News table, filter your query as you need
        $allNewsQuery = $newsRepository->createQueryBuilder('n')->getQuery();

        // Paginate the results of the query
        $news = $paginator->paginate(
        // Doctrine Query, not results
            $allNewsQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            2
        );

        // Render the twig view
        return $this->render('news/index.html.twig', [
            'news' => $news
        ]);
    }

    /**
     * @Route("/news/{id}", name="news_show")
     */
    public function show($id)
    {
        $news = $this->getDoctrine()
            ->getRepository(News::class)
            ->find($id);

        if (!$news) {
            throw $this->createNotFoundException(
                'No news found for id ' . $id
            );
        }

        return new Response($news->getTextNews());

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }
}
