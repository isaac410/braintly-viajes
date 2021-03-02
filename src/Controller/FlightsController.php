<?php

namespace App\Controller;

use App\Entity\Flights;
use App\Service\Helpers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/flights")
 */
class FlightsController extends AbstractController {

    /**
     * @Route("/recommendation", name="flights_recommendation", methods={"GET", "OPTIONS"})
     */
    public function recommendation(Request $request, Helpers $helpers) {

        // test : http://localhost/braintly-viajes/public/index.php/api/flights/recommendation?occupants=2&departure_airport=MDQ&arrival_airport=YOW&check_in=2020-12-14&check_out=2020-12-18&type=economic

        try {
            // Get parameters by query string
            //$params = $request->query->all();
            // Ini parameters example:
            $params = [
                "occupants" => 2,
                "departure_airport" => "MDQ",
                "arrival_airport" => "YOW",
                "check_in" => "2020-12-14", 
                "check_out" => "2020-12-18", 
                "type" => "economic"
            ];
            $params = array_merge($params, $request->query->all());
            $departureFlights = $this->getDoctrine()->getRepository(Flights::class)->findFlights( $params );

            $parametersReturn = [
                "occupants" => $params["occupants"],
                "departure_airport" => $params["arrival_airport"],
                "arrival_airport" => $params["departure_airport"],
                "check_in" => $params["check_out"], 
                "check_out" => $params["check_out"], 
                "type" => $params["type"]
            ];
            $returnFlights = $this->getDoctrine()->getRepository(Flights::class)->findFlights( $parametersReturn );

            // return response
            return new JsonResponse([
                'ok' => true,
                'data' => ["departures" => $departureFlights, "returns" => $returnFlights],
                'message' => 'Success!'
            ], 200);

        } catch (\Exception $ex) {
            return new JsonResponse(['message' => 'Operation failed. ' . $ex->getMessage(), 'ok' => false], 500);
        }
    }

}
