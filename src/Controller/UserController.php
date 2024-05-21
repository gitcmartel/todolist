<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\UserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * The User controller.
 *
 * This controller allows you to create, list and edit application users.
 */
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $manager;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * UserController constructor.
     *
     * @param EntityManagerInterface $manager Entity manager for ORM operations.
     * @param UserRepository $userRepository Repository for user-related operations.
     * @param UserPasswordHasherInterface $passwordHasher Password hashing service.
     */
    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher) 
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Display the users list.
     *
     * @return Response Rendering the user list.
     */

    #[Route('/users', name: 'app_user_list')]
    public function list()
    {
        return $this->render('user/list.html.twig', [
            'users' => $this->userRepository->findAll()
        ]);
    }

    /**
     * Creates a new user.
     *
     * @param Request $request HTTP request.
     * @return Response Rendering the user creation.
     */
    #[Route('/users/create', name: 'app_user_create')]
    public function create(Request $request) : Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user, 
                $user->getPassword()
            );

            $user->setPassword($hashedPassword);

            $user->setRoles([$form->get('role')->getData()]);

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('app_user_list');
        }
        
        return $this->render('user/create.html.twig', [
            'controller_name' => 'UserController',
            'formUser' => $form
        ]);
    }

    /**
     * Édit an existing user.
     *
     * @param User $user The user to edit.
     * @param Request $request HTTP request.
     * @return Response Rendering the user edit.
     */
    #[Route('/users/{id}/edit', name: 'app_user_edit')]
    public function edit(User $user, Request $request)
    {
        $currentRole = $user->getRoles()[0] ?? 'ROLE_USER';

        $form = $this->createForm(UserFormType::class, $user, [
            'current_role' => $currentRole
        ]);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user, 
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $user->setRoles([$form->get('role')->getData()]);

            $this->manager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/edit.html.twig', [
            'controller_name' => 'UserController', 
            'formUser' => $form, 
            'user' => $user
        ]);
    }
}
