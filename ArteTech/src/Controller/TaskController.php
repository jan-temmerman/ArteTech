<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private function isAdmin()
    {
        $user = $this->getUser();
        if($user->getStatus()->getStatus() === 'admin') {
            return true;
        } else
            return false;
    }

    /**
     * @Route("/tasks/{id}", name="task_detail")
     * @param $id
     * @return Response
     */
    public function detail($id)
    {
        $task = "";
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(Task::class);
            $task = $repository->find($id);

        } else
            $isUnauthorized = true;
        return $this->render('task/detail.html.twig', [
            'title' => 'Taak Details',
            'task' => $task,
            'isUnauthorized' => $isUnauthorized
        ]);
    }
}
