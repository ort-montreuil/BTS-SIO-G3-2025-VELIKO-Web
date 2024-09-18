<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    # = attributs
    # @= annotations
    #[Route('/home')]
    public function Home(): Response
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_information.json?=&=&=",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            var_dump(curl_error($curl));
        } else {
            // echo json_decode($response,associative: true);
            $response = json_decode($response, true);

        }

        curl_close($curl);


        $curl2 = curl_init();

        curl_setopt_array($curl2, [
            CURLOPT_URL => "https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_status.json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false,

        ]);

        $status = curl_exec($curl2);
        $err = curl_error($curl2);

        curl_close($curl2);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $status = json_decode($status, true);
        }

        $stationData = [];//tableau vide pour inserer tt les donnees des deux urls

        //cette boucle parcourt chaque element dans le 1e tableau response (1er url)  et chaque element represente les infos de la station du 1er url
        foreach ($response['data']['stations'] as $stationInfos) {
            //cette boucle parcourt chaque element dans le 2e tableau status(2e url) et chaque element represente les infos de status cad (2e url)
            foreach ($status['data']['stations'] as $statusInfos) {
                //si les numeros des stations des 2 urls sont les memes alors
                if ($statusInfos['station_id'] == $stationInfos['station_id']) {
                    //on cree un tableau status data où l'on va stocker toutes les donnees qu'on a besoin que ce soit dans le 1e url ou 2e url
                    $statusData = [
                        'nom' => $stationInfos['name'],
                        'lat' => $stationInfos['lat'],
                        'lon' => $stationInfos['lon'],
                        'numBikesAvailable' => $statusInfos["num_bikes_available"],
                        'veloMecanique' => $statusInfos["num_bikes_available_types"][0]['mechanical'],
                        'veloElectrique' => $statusInfos["num_bikes_available_types"][1]['ebike']
                    ];
                    //on ajoute au tableau vide , celui de depart , toute les donnees qui sont dans le tableau statusdata
                    $stationData[] = $statusData;
                }
            }
        }

        return $this->render('home/home.html.twig', [
            'titre' => '',
            "response" => $response,
            "status" => $status,
            "stationData" => $stationData,
        ]);

    }
}

