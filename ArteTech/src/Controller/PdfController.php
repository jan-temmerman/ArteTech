<?php

namespace App\Controller;

use App\Entity\Period;
use App\Entity\Company;
use Dompdf\Dompdf;
use Dompdf\Options;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
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
     * @Route("/periods/{id}/pdf", name="period_detail_pdf")
     * @param $id
     * @return Response
     */
    public function periodDetail($id)
    {
        $period = "";
        $isUnauthorized = false;

        if($this->isAdmin() || $this->isClient()) {
            $repository = $this->getDoctrine()->getRepository(Period::class);
            $period = $repository->find($id);

        } else
            $isUnauthorized = true;

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Open Sans');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $difference = 0;
        $totalHours = 0;
        $totalKm = 0;
        $extraCosts = 0;
        foreach ($period->getTasks() as $task) {
            $time1 = strtotime($task->getStartTime()->format('H:i:s'));
            $time2 = strtotime($task->getEndTime()->format('H:i:s'));
            $difference = round(abs($time2 - $time1) / 3600, 2);
            $totalHours += $difference;

            if($task->getDate()->format('D') === 'Sat')
                $extraCosts += 0.5 * ($difference * $period->getHourlyRate()->getPrice());
            elseif($task->getDate()->format('D') === 'Sun')
                $extraCosts += 1 * ($difference * $period->getHourlyRate()->getPrice());
            elseif(round(abs($time2 - $time1) / 3600, 2) > 8) {
                $extraHours = $difference - 8;
                $extraCosts += 0.2 * ($extraHours * $period->getHourlyRate()->getPrice());
            }

            $totalKm += $task->getKmTraveled();
        }

        $totalPrice = $totalHours * $period->getHourlyRate()->getPrice() + $extraCosts;
        $totalPrice += $totalKm * $period->getTransportRate()->getPrice();

        $sideInfo = new Object_();
        $sideInfo->totalKm = $totalKm;
        $sideInfo->totalPrice = $totalPrice;
        $sideInfo->totalHours = $difference;

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('pdf/period.html.twig', [
            'title' => 'Opdrachten',
            'period' => $period,
            'sideInfo' => $sideInfo,
            'userStatus' => $this->getUserStatus(),
            'isUnauthorized' => $isUnauthorized,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // Render the HTML as PDF
        $dompdf->render();

        $today = date('d-m-Y');

        $fileName = $period->getCompany()->getName()  . '_' . $period->getId() . '_' . $today . '.pdf';

        //dd($fileName);

        // Output the generated PDF to Browser (force download)
        $dompdf->stream($fileName, [
            "Attachment" => false
        ]);

        return $this->render('period/detail.html.twig', [
            'title' => 'Opdracht Details',
            'period' => $period,
            'totalHours' => $difference,
            'userStatus' => $this->getUserStatus(),
            'isUnauthorized' => $isUnauthorized
        ]);
    }
}
