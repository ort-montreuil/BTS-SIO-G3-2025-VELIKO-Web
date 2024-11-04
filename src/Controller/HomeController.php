<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/carte', name: 'app_home')]
    public function home(): Response
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => $_ENV["API_VELIKO_PORT"],
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
            CURLOPT_PORT => $_ENV["API_VELIKO_PORT"],
            CURLOPT_URL => $_ENV["API_VELIKO_URL"] . "/api/stations",
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

        $stationData = [];

        foreach ($response as $stationInfos) {
            foreach ($status as $statusInfos) {
                if ($statusInfos['station_id'] == $stationInfos['station_id']) {
                    $statusData = [
                        'nom' => $stationInfos['name'],
                        'lat' => $stationInfos['lat'],
                        'lon' => $stationInfos['lon'],
                        'numBikesAvailable' => $statusInfos['num_bikes_available'] ?? 0,
                        'veloMecanique' => $statusInfos['num_bikes_available_types'][0]['mechanical'] ?? 0,
                        'veloElectrique' => $statusInfos['num_bikes_available_types'][1]['ebike'] ?? 0,
                    ];
                    $stationData[] = $statusData;
                }
            }
        }

        return $this->render('home/home.html.twig', [
            'titre' => '',
            'stationData' => $stationData,
        ]);
    }
}