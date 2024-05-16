<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class TaskController extends AbstractController
{
    private TaskRepository $taskRepository;
    private EntityManagerInterface $manager;

    public function __construct(TaskRepository $taskRepository, EntityManagerInterface $manager)
    {
        $this->taskRepository = $taskRepository;
        $this->manager = $manager;
    }

    #[Route('/tasks', name: 'app_task_list')]
    public function listAction()
    {
        return $this->render('task/list.html.twig', ['tasks' => $this->taskRepository->findAll()]);
    }


    #[Route('/tasks/create', name: 'app_task_create')]
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);

        $form->handleRequest($request);
        dump($form);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->persist($task);
            $this->manager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }


    #[Route('/tasks/{id}/edit', name: 'app_task_edit')]
    public function editAction(Task $task, Request $request)
    {
        $form = $this->createForm(TaskFormType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }


    #[Route('/tasks/{id}/toggle', name: 'app_task_toggle')]
    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->getIsDone());
        $this->manager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }


    #[Route('/tasks/{id}/delete', name: 'app_task_delete')]
    public function deleteTaskAction(Task $task)
    {
        $this->manager->remove($task);
        $this->manager>flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
