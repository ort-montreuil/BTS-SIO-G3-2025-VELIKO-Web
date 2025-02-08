<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Station;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\StationRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class ReservationController extends AbstractController
{
    private StationRepository $stationRepository;
    private ReservationRepository $reservationRepository;


    public function __construct(
        StationRepository $stationRepository,
        ReservationRepository $reservationRepository

    ) {
        $this->stationRepository = $stationRepository;
        $this->reservationRepository = $reservationRepository;

    }

    #[Route('/reservation', name: 'app_reservation')]
    public function reservation(Request $request,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $stationNames = [];
        $stations = $this->stationRepository->findAll();

        foreach ($stations as $station) {
            $stationNames[] = [
                'name' => $station->getName(),
                'id' => $station->getStationId(),
            ];
        }

        if ($request->isMethod('POST')) {
            // Récupérer les IDs des stations depuis le formulaire
            $dateReservation = new \DateTime();
            $stationDepartId = $request->get('stationDepart');
            $stationFinId = $request->get('stationFin');


            // Récupérer les stations de la base de données
            $stationDepart = $this->stationRepository->find($stationDepartId);
            $stationFin = $this->stationRepository->find($stationFinId);

            // Créer la réservation
            $reservation = new Reservation();
            $reservation->setDateReservation($dateReservation);
            $reservation->setStationDepart($stationDepart->getName()); // On stocke le nom de la station, ou l'objet station selon ton modèle
            $reservation->setStationFin($stationFin->getName()); // Même ici
            $reservation->setIdUser($user);

            // Sauvegarder la réservation dans la base de données
            $entityManager->persist($reservation);
            $entityManager->flush();


        }




        return $this->render('reservation/index.html.twig', [
            'station_names' => $stationNames,
            'stations' => $stations,

        ]);
    }
}
