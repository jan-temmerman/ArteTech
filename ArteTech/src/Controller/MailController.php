<?php

namespace App\Controller;

use App\Entity\Period;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    /**
     * @Route("/periods/{id}/sendNotification", name="period_sendNotif")
     * @param MailerInterface $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmail(MailerInterface $mailer, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Period::class);
        $period = $repository->find($id);

        $email = (new TemplatedEmail())
            ->from('info@artetech.com')
            ->to('jantemme@student.arteveldehs.be')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Er is een opdracht afgerond.')
            ->htmlTemplate('mail/notif.html.twig')
            ->context([
                'period' => $period
            ]);

        /** @var Symfony\Component\Mailer\SentMessage $sentEmail */
        $sentEmail = $mailer->send($email);
        // $messageId = $sentEmail->getMessageId();
        return $this->redirectToRoute('periods');
        // ...
    }
}
