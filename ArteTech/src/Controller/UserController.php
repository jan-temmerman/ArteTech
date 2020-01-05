<?php

namespace App\Controller;

use App\Entity\HourlyRate;
use App\Entity\Task;
use App\Entity\TransportRate;
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
use Symfony\Component\HttpFoundation\Response;
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

    private function isClient()
    {
        $user = $this->getUser();
        if($user) {
            if ($user->getStatus()->getStatus() === 'client') {
                return true;
            } else
                return false;
        } else
            return false;
    }

    private function getUserStatus()
    {
        $user = $this->getUser();
        if($user)
            return $user->getStatus()->getStatus();
        else
            return "guest";
    }

    /**
     * @Route("/users/{id}", name="user_detail")
     * @param $id
     * @return Response
     */
    public function detail($id)
    {
        $user = "";
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->find($id);

        } else
            $isUnauthorized = true;

        return $this->render('user/detail.html.twig', [
            'title' => 'Gebruiker Details',
            'user' => $user,
            'userStatus' => $this->getUserStatus(),
            'isUnauthorized' => $isUnauthorized
        ]);
    }

    /**
     * @Route("/users_add", name="addUser")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function add(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $isUnauthorized = false;
        $formView = "";

        if($this->isAdmin()) {
            $user = new User();

            $form = $this->createFormBuilder($user)
                ->add('firstName', TextType::class,  ['label' => 'Voornaam'])
                ->add('lastName', TextType::class, ['label' => 'Achternaam'])
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
                ->add('hourly_rate', EntityType::class, [
                    'label' => 'Uurloon',
                    'class' => HourlyRate::class,
                    'choice_label' => function ($hourlyRate) {
                        return $hourlyRate->getPrice() . ' ' . $hourlyRate->getUnit();
                    }])
                ->add('transport_rate', EntityType::class, [
                    'label' => 'Transport Vergoeding',
                    'class' => TransportRate::class,
                    'choice_label' => function ($transportRate) {
                        return $transportRate->getPrice() . ' ' . $transportRate->getUnit();
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
                $user->setIsActive(true);
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect("/users");
            }
            $formView = $form->createView();
        } else
            $isUnauthorized = true;

        return $this->render('user/add.html.twig', [
            'form' => $formView,
            'title' => 'Gebruiker Toevoegen',
            'userStatus' => $this->getUserStatus(),
            'isUnauthorized' => $isUnauthorized,
        ]);
    }

    /**
     * @Route("/users/{id}/edit", name="edit_user")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, UserPasswordEncoderInterface $encoder, $id)
    {
        $isUnauthorized = false;
        $formView = "";

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->find($id);

            $form = $this->createFormBuilder($user)
                ->add('firstName', TextType::class,  ['label' => 'Voornaam'])
                ->add('lastName', TextType::class, ['label' => 'Achternaam'])
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
                ->add('hourly_rate', EntityType::class, [
                    'label' => 'Uurloon',
                    'class' => HourlyRate::class,
                    'choice_label' => function ($hourlyRate) {
                        return $hourlyRate->getPrice() . ' ' . $hourlyRate->getUnit();
                    }])
                ->add('transport_rate', EntityType::class, [
                    'label' => 'Transport Vergoeding',
                    'class' => TransportRate::class,
                    'choice_label' => function ($transportRate) {
                        return $transportRate->getPrice() . ' ' . $transportRate->getUnit();
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
            $formView = $form->createView();
        } else
            $isUnauthorized = true;

        return $this->render('user/add.html.twig', [
            'form' => $formView,
            'title' => 'Gebruiker Toevoegen',
            'userStatus' => $this->getUserStatus(),
            'isUnauthorized' => $isUnauthorized,
        ]);
    }

    /**
     * @Route("/users/{id}/deactivate", name="deactivate_user")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deactivate($id)
    {

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->find($id);

            $entityManager = $this->getDoctrine()->getManager();
            $user->setIsActive(false);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->redirect("/users");
    }
}
