<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\StationRepository;
use App\Repository\ReservationRepository;
use App\Service\TokenService;
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
        StationRepository     $stationRepository,
        ReservationRepository $reservationRepository

    )
    {
        $this->stationRepository = $stationRepository;
        $this->reservationRepository = $reservationRepository;

    }

    #[Route('/reservation', name: 'app_reservation')]
    public function reservation(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }


        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => "9042",
            CURLOPT_URL => "http://localhost:9042/api/velo/location?",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => [
                "Authorization: RG6F8do7ERFGsEgwkPEdW1Feyus0LXJ21E2EZRETTR65hN9DL8a3O8a",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            json_decode($response, true);
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
            $dateReservation = $request->get('date_reservation');
            $stationDepartId = $request->get('stationDepart');
            $stationFinId = $request->get('stationFin');

            $dateReservation = \DateTime::createFromFormat('Y-m-d', $dateReservation);

            // Récupérer les stations de la base de données
            $stationDepart = $this->stationRepository->find($stationDepartId);
            $stationFin = $this->stationRepository->find($stationFinId);

            // Créer la réservation
                $reservation = new Reservation();
                $reservation->setDateReservation($dateReservation);
                $reservation->setStationDepart($stationDepart->getName()); // On stocke le nom de la station
                $reservation->setStationFin($stationFin->getName()); // Même ici
                $reservation->setIdUser($user);

                // Sauvegarder la réservation dans la base de données
                $entityManager->persist($reservation);
                $entityManager->flush();

                $this->addFlash('success', 'Vous avez loué un vélo avec succès !');

            }



            return $this->render('reservation/index.html.twig', [
                'station_names' => $stationNames,
                'stations' => $stations,

            ]);
        }
    }








