<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\HourlyRate;
use App\Entity\PauseLength;
use App\Entity\Period;
use App\Entity\Task;
use App\Entity\TransportRate;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PeriodController extends AbstractController
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
     * @Route("/periods/{id}", name="period_detail")
     * @param $id
     * @return Response
     */
    public function detail($id)
    {
        $period = "";
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(Period::class);
            $period = $repository->find($id);

        } else
            $isUnauthorized = true;

        $difference = 0;
        $totalKm = 0;
        foreach ($period->getTasks() as $task) {
            $time1 = strtotime($task->getStartTime()->format('H:i:s'));
            $time2 = strtotime($task->getEndTime()->format('H:i:s'));
            $difference += round(abs($time2 - $time1) / 3600, 2);

            $totalKm += $task->getKmTraveled();
        }

        $totalPrice = $difference * $period->getHourlyRate()->getPrice();
        $totalPrice += $totalKm * $period->getTransportRate()->getPrice();

        $sideInfo = new Object_();
        $sideInfo->totalKm = $totalKm;
        $sideInfo->totalPrice = $totalPrice;
        $sideInfo->totalHours = $difference;

        return $this->render('period/detail.html.twig', [
            'title' => 'Opdracht Details',
            'period' => $period,
            'sideInfo' => $sideInfo,
            'isUnauthorized' => $isUnauthorized
        ]);
    }

    /**
     * @Route("/periods_add", name="add_period")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add(Request $request)
    {
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $period = new Period();

            $form = $this->createFormBuilder($period)
                ->add('company', EntityType::class, [
                    'label' => 'Klant',
                    'class' => Company::class,
                    'choice_label' => function ($company) {
                        return $company->getName();
                    }])
                ->add('startDate', DateType::class, ['widget' => 'single_text', 'label' => 'Startdatum'])
                ->add('endDate', DateType::class, ['widget' => 'single_text', 'label' => 'Einddatum'])
                ->add('name', TextType::class, ['label' => 'Opdracht'])
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
                ->add('save', SubmitType::class, ['label' => 'Opslaan'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $period = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($period);
                $entityManager->flush();

                return $this->redirect("/periods");
            }
        } else
            $isUnauthorized = true;

        return $this->render('task/add.html.twig', [
            'form' => $form->createView(),
            'title' => 'Opdracht Toevoegen',
            'isUnauthorized' => $isUnauthorized,
        ]);
    }

    /**
     * @Route("/periods/{id}/edit", name="edit_period")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, $id)
    {
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(Period::class);
            $period = $repository->find($id);

            $form = $this->createFormBuilder($period)
                ->add('company', EntityType::class, [
                    'label' => 'Klant',
                    'class' => Company::class,
                    'choice_label' => function ($company) {
                        return $company->getName();
                    }])
                ->add('startDate', DateType::class, ['widget' => 'single_text', 'label' => 'Startdatum'])
                ->add('endDate', DateType::class, ['widget' => 'single_text', 'label' => 'Einddatum'])
                ->add('name', TextType::class, ['label' => 'Opdracht'])
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
                ->add('save', SubmitType::class, ['label' => 'Opslaan'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $period = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($period);
                $entityManager->flush();

                return $this->redirect("/periods");
            }
        } else
            $isUnauthorized = true;

        return $this->render('task/add.html.twig', [
            'form' => $form->createView(),
            'title' => 'Opdracht Bijwerken',
            'isUnauthorized' => $isUnauthorized,
        ]);
    }
}
