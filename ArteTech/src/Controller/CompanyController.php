<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\HourlyRate;
use App\Entity\Period;
use App\Entity\TransportRate;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
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
     * @Route("/companies_add", name="add_company")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add(Request $request)
    {
        $isUnauthorized = false;
        $formView = "";

        if($this->isAdmin()) {
            $company = new Company();

            $form = $this->createFormBuilder($company)
                ->add('name', TextType::class, ['label' => 'Naam Bedrijf'])
                ->add('admin', EntityType::class, [
                    'label' => 'Administrator',
                    'class' => User::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->andWhere('u.status = :client and u.isActive = :bool')
                            ->setParameter('client', 2)
                            ->setParameter('bool', true)
                            ->orderBy('u.lastName', 'ASC');
                    },
                    'choice_label' => function ($user) {
                        return ucfirst($user->getFirstname()) . ' ' . ucfirst($user->getLastname()) . ' - ' . ucfirst($user->getStatus()->getStatus());
                    }])
                ->add('save', SubmitType::class, ['label' => 'Opslaan'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $company = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($company);
                $entityManager->flush();

                return $this->redirect("/companies");
            }
            $formView = $form->createView();
        } else
            $isUnauthorized = true;

        return $this->render('task/add.html.twig', [
            'form' => $formView,
            'title' => 'Klant Toevoegen',
            'userStatus' => $this->getUserStatus(),
            'isUnauthorized' => $isUnauthorized,
        ]);
    }

    /**
     * @Route("/companies/{id}/edit", name="edit_company")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, $id)
    {
        $isUnauthorized = false;
        $formView = "";

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(Company::class);
            $company = $repository->find($id);

            $form = $this->createFormBuilder($company)
                ->add('name', TextType::class, ['label' => 'Naam Bedrijf'])
                ->add('admin', EntityType::class, [
                    'label' => 'Administrator',
                    'class' => User::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.status = :client')
                            ->setParameter('client', 2)
                            ->orderBy('u.lastName', 'ASC');
                    },
                    'choice_label' => function ($user) {
                        return ucfirst($user->getFirstname()) . ' ' . ucfirst($user->getLastname()) . ' - ' . ucfirst($user->getStatus()->getStatus());
                    }])
                ->add('save', SubmitType::class, ['label' => 'Opslaan'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $company = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($company);
                $entityManager->flush();

                return $this->redirect("/companies");
            }
            $formView = $form->createView();
        } else
            $isUnauthorized = true;

        return $this->render('task/add.html.twig', [
            'form' => $formView,
            'title' => 'Klant Toevoegen',
            'userStatus' => $this->getUserStatus(),
            'isUnauthorized' => $isUnauthorized,
        ]);
    }

    /**
     * @Route("/companies/{id}/delete", name="delete_company")
     * @param $id
     * @return RedirectResponse|Response
     */
    public function delete($id)
    {
        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(Company::class);
            $company = $repository->find($id);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($company);
            $entityManager->flush();

            return $this->redirect("/companies");
        }
        return $this->redirect("/companies/" . $id);
    }
}
