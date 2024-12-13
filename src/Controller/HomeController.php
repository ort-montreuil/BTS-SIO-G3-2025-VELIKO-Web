<?php

namespace App\Controller;

use App\Entity\StationUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }
    #[Route('/carte', name: 'app_home')]
    public function home(): Response
    {
        $user = $this->getUser();
        if ($user)
        {
            if ($user->isBooleanChangerMdp())
            {
                return $this->redirectToRoute("app_change_mdp_force");
            }
        }
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $_ENV["API_VELIKO_URL"] . "/api/stations",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
        }

        $curl2 = curl_init();

        curl_setopt_array($curl2, [
            CURLOPT_URL => $_ENV["API_VELIKO_URL"] . "/api/stations/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $status = curl_exec($curl2);
        $err2 = curl_error($curl2);

        curl_close($curl2);

        if ($err2) {
            echo "cURL Error #:" . $err2;
        } else {
            $status = json_decode($status, true);
        }
        $stationData = [];//tableau vide pour inserer tt les donnees des deux urls


        //cette boucle parcourt chaque element dans le 1e tableau response (1er url)  et chaque element represente les infos de la station du 1er url
        foreach ($response as $stationInfos) {
            //cette boucle parcourt chaque element dans le 2e tableau status(2e url) et chaque element represente les infos de status cad (2e url)
            foreach ($status as $statusInfos) {
                //si les numeros des stations des 2 urls sont les memes alors
                if ($statusInfos['station_id'] == $stationInfos['station_id']) {
                    //on cree un tableau status data où l'on va stocker toutes les donnees qu'on a besoin que ce soit dans le 1e url ou 2e url
                    $statusData = [
                        'id' => $stationInfos['station_id'],
                        'nom' => $stationInfos['name'],
                        'lat' => $stationInfos['lat'],
                        'lon' => $stationInfos['lon'],
                        'capacity' => $stationInfos["capacity"],
                        'veloMecanique' => $statusInfos["num_bikes_available_types"][0]['mechanical'],
                        'veloElectrique' => $statusInfos["num_bikes_available_types"][1]['ebike']
                    ];
                    //on ajoute au tableau vide , celui de depart , toute les donnees qui sont dans le tableau statusdata
                    $stationData[] = $statusData;
                }
            }

        }
        $user = $this->getUser();
        $favoriteStationIds = [];

        if ($user) {
            $stationUserRepository = $this->entityManager->getRepository(StationUser::class);

            // Obtenir les stations favorites de l'utilisateur
            $favorites = $stationUserRepository->findBy(['idUser' => $user->getId()]);
            $favoriteStationIds = array_map(function ($favorite) {
                return $favorite->getIdStation();
            }, $favorites);
        }
        return $this->render('home/home.html.twig', [
            'titre' => '',
            'stationData' => $stationData,
            'favoriteStationIds' => $favoriteStationIds
        ]);
    }
}