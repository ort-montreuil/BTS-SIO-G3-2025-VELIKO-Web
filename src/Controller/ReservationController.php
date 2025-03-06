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
        if (!$user) {
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
            $dateReservation = $request->get('date_reservation');
            $stationDepartId = $request->get('stationDepart');
            $stationFinId = $request->get('stationFin');
            $typeVelo = $request->get('typeVelo');

            $response = $this->makeCurl("/api/velos", "GET", "");

            foreach ($response as $velo) {
                $idVelo = $velo["velo_id"];

                // Vérifier si le vélo est disponible à la station de départ
                if ($velo["station_id_available"] == $stationDepartId
                    && $velo["status"] == "available"
                    && ($velo["type"] == "ebike" || $velo["type"] == "mechanical")) {

                    // Mettre le vélo en location
                    $this->makeCurl("/api/velo/{$idVelo}/location", "PUT", "RG6F8do7ERFGsEgwkPEdW1Feyus0LXJ21E2EZRETTR65hN9DL8a3O8a");

                    // Vérifier si le vélo est en location et doit être ramené à la station de fin
                    if ($velo["station_id_available"] != $stationFinId
                        && $velo["status"] == "location") {

                        // Restauration du vélo à la station de fin
                        $this->makeCurl("/api/velo/{$idVelo}/restore/{$stationFinId}", "PUT", "RG6F8do7ERFGsEgwkPEdW1Feyus0LXJ21E2EZRETTR65hN9DL8a3O8a");

                    }


                    // Créer la réservation
                    $dateReservation = \DateTime::createFromFormat('Y-m-d', $dateReservation);
                    $stationDepart = $this->stationRepository->find($stationDepartId);
                    $stationFin = $this->stationRepository->find($stationFinId);

                    $reservation = new Reservation();
                    $reservation->setDateReservation($dateReservation);
                    $reservation->setStationDepart($stationDepart->getName());
                    $reservation->setStationFin($stationFin->getName());
                    $reservation->setTypeVelo($typeVelo);
                    $reservation->setIdUser($user);

                    $entityManager->persist($reservation);
                    $entityManager->flush();

                    $this->addFlash('success', 'Vous avez loué un vélo avec succès !');

                    break; // Sortir de la boucle après avoir réservé un vélo
                }
            }
        }


            return $this->render('reservation/index.html.twig', [
                'station_names' => $stationNames,
                'stations' => $stations,

            ]);
        }

        public
        function makeCurl(string $url, string $methode, string $token)
        {

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_PORT => "9042",
                CURLOPT_URL => $_ENV["API_VELIKO_URL"] . $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $methode,
                CURLOPT_POSTFIELDS => "",
                CURLOPT_HTTPHEADER => [
                    "Authorization:" . $token]
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                $response = json_decode($response, true);

            }

            return $response;
        }
    }









