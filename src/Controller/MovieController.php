<?php

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;





#[Route('/movie', name: 'movie_')]
class MovieController extends AbstractController
{
    #[Route('/new', name:'new_movie', methods: 'POST')]
    public function newMovie( ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $movie = new Movie();
        $movie->setMovieName($request->get('movie_name'));
        $movie->setDirector($request->get('director'));
        $movie->setYear($request->get('year'));

        $entityManager->persist($movie);
        $entityManager->flush();

        return $this->json('Added new movie to the database with id ' . $movie->getId());
    }

    #[Route('/{id}', name:'one_movie', methods: 'GET')]
    public function movie(ManagerRegistry $doctrine, int $id): Response
    {
        $movie = $doctrine->getRepository(Movie::class)->find($id);
        if(!$movie){
            return $this->json('No found movie with id ' . $id);
        } else {
            $data = [
                'id' => $movie->getId(),
                'movie_name' => $movie->getMovieName(),
                'director' => $movie->getDirector(),
                'year' => $movie->getYear(),
            ];
            return $this->json($data);
        }
    }

    #[Route('/', name: 'movies', methods: 'GET')]
    public function movies(ManagerRegistry $doctrine): Response
    {
        $movies = $doctrine->getRepository(Movie::class)->findAll();

        $data = [];
        foreach ($movies as $movie){
            $data[] = [
                'id' => $movie->getId(),
                'movie_name' => $movie->getMovieName(),
                'director' => $movie->getDirector(),
                'year' => $movie->getYear(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/edit/{id}', name: 'edit_movie', methods: 'PUT' )]
    public function editMovie(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $movie = $doctrine->getRepository(Movie::class)->find($id);
        if(!$movie){
            return $this->json('No found movie with id ' . $id);
        } else {
            $movie->setMovieName($request->get('movie_name'));
            $movie->setDirector($request->get('director'));
            $movie->setYear($request->get('year'));
            $entityManager->flush();

            $data[] = [
                'id' => $movie->getId(),
                'movie_name' => $movie->getMovieName(),
                'director' => $movie->getDirector(),
                'year' => $movie->getYear(),
            ];

            return $this->json($data);
        }
    }
    #[Route('/delete/{id}', name: 'delete_movie', methods: 'DELETE')]
    public function deleteMovie(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $movie = $entityManager->getRepository(Movie::class)->find($id);

        if(!$movie){
            return $this->json('Movie not found');
        } else {
            $entityManager->remove($movie);
            $entityManager->flush();

            return $this->json('Deleted movie with id '. $id);
        }
    }
}
