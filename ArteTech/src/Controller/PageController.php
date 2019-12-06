<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserStatus;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="page")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        $repository = $this->getDoctrine()->getRepository(UserStatus::class);
        $statuses = $repository->findAll();
        $status = $statuses[0];

        $user = new User();
        $user->setStatus($status);

        $form = $this->createFormBuilder($user)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('status', EntityType::class, [
                'class' => UserStatus::class,
                'choice_label' => function ($status) {
                    return ucfirst($status->getStatus());
                }])
            ->add('save', SubmitType::class, ['label' => 'Save user'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);
            $entityManager->flush();

            return $this->redirect("/");
        }

        return $this->render('page/index.html.twig', [
            'users' => $users,
            'statuses' => $statuses,
            'form' => $form->createView(),
        ]);
    }
}
