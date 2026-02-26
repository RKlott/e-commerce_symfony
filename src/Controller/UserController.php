<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_users')]
    public function index(UserRepository $repo): Response
    {
        $users = $repo->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/update/{id}', name: 'app_user_update')]
    public function updateUser(User $user, EntityManagerInterface $entityManager, Request $request) : Response
    {
        //TODO: Essayer de faire la logique d'update d'utilisateur en récupérant bien les rôles
    return $this->render('user/updateUser.html.twig', [

    ]);
    }
}
