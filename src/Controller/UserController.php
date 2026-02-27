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

    //TODO: se renseigner sur l'utilisation de la méthode "array_intersect" pour comparer les valeurs de deux tableaux

    #[Route('/admin/user', name: 'app_users')]
    public function index(UserRepository $repo): Response
    {
        $users = $repo->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/remove/{id}', name: 'app_user_remove')]
    public function ruserRemove(EntityManagerInterface $entityManager,$id,  UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('danger', "L'utilisateur à bien été supprimé.");
        
        return $this->redirectToRoute('app_users');
    }

    #[Route('/admin/user/{id}/addEditor/{role}', name: 'app_user_add_editor_role')]
    public function addEditorRole(User $user, EntityManagerInterface $entityManager, Request $request, $role): Response
    {
        $rolesWhitelist = ['ROLE_EDITOR', 'ROLE_USER'];


        if (!in_array($role, $rolesWhitelist, true)) {
            $this->addFlash('error', "Le rôle demandé n'existe pas.");
            return $this->redirectToRoute('app_users');
        }

        if ($role !== 'ROLE_USER') {
            $user->setRoles(['ROLE_EDITOR', 'ROLE_USER']);
        } else {
            $user->setRoles($role);
        }

        $entityManager->flush();

        $this->addFlash('success', `Le rôle à bien été ajouté à l'utilisateur.`);
        return $this->redirectToRoute('app_users');
    }

    #[Route('/admin/user/{id}/removeEditorRole', name: 'app_user_remove_editor_role')]
    public function removeEditorRole(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setRoles([]);
        $entityManager->flush();
        $this->addFlash('danger', "Le rôle éditeur à été retiré de cet utilisateur.");

        return $this->redirectToRoute('app_users');
    }
}
