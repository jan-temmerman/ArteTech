<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
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
     * @Route("/users/add", name="addUser")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $user = new User();

            $form = $this->createFormBuilder($user)
                ->add('firstName', TextType::class)
                ->add('lastName', TextType::class)
                ->add('email', EmailType::class, [
                    'attr' => ['autocomplete' => 'off'],
                    'error_bubbling' => true,
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'attr' => ['autocomplete' => 'off'],
                    'invalid_message' => 'The password fields must match.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'error_bubbling' => true,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repeat Password'],
                ])
                ->add('status', EntityType::class, [
                    'class' => UserStatus::class,
                    'choice_label' => function ($status) {
                        return ucfirst($status->getStatus());
                    }])
                ->add('save', SubmitType::class, ['label' => 'Save user'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $plainPassword = $user->getPassword();
                $encoded = $encoder->encodePassword($user, $plainPassword);
                $user->setPassword($encoded);
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect("/users");
            }
        } else
            $isUnauthorized = true;

        return $this->render('user/add.html.twig', [
            'form' => $form->createView(),
            'title' => 'Add User',
            'isUnauthorized' => $isUnauthorized,
        ]);
    }
}
