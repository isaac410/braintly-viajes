<?php

namespace App\Repository;

use App\Entity\Flights;
use App\Service\Helpers;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Flights|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flights|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flights[]    findAll()
 * @method Flights[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlightsRepository extends ServiceEntityRepository {

    public $helpers;

    public function __construct(ManagerRegistry $registry, Helpers $helpers) {
        parent::__construct($registry, Flights::class);
        $this->helpers = $helpers;
    }

    public function findFlights($param = null){

        // flights aperture without scale
        $departue_flights = $this->sqlFlights($param);
        $count = count($departue_flights);
        $departue_flights_scale = [];
        // if distance > 5000 search scale flights
        if( $count > 0 && $departue_flights[0]["distance"] > 5000 || $count == 0){
            // get flights aperture scale ids 
            $sql = "SELECT f1.id as flights_f, f2.id as flights_s FROM flights f1 INNER JOIN flights f2 ON f2.departure_airport_id = f1.arrival_airport_id LEFT JOIN airports aps_1 ON aps_1.id = f1.departure_airport_id LEFT JOIN airports aps_2 ON aps_2.id = f1.arrival_airport_id LEFT JOIN airports aps_3 ON aps_3.id = f2.arrival_airport_id WHERE aps_1.iata_code = \"".$param['departure_airport']."\" AND aps_3.iata_code = \"".$param['arrival_airport']."\" AND f1.status = 'scheduled' AND f2.status = 'scheduled' AND f1.departure_date > DATE_ADD(\"".$param['check_in']."\", INTERVAL -1 DAY) AND f2.departure_date > DATE_ADD(\"".$param['check_in']."\", INTERVAL -1 DAY) AND f2.departure_date > f1.departure_date LIMIT 2";
            $departue_flights_scale_ids = $this->helpers->aplicateConn($sql);
            // find properties of flights aperture scale ids
            foreach ($departue_flights_scale_ids as $key => $flights) {
                $flights_scale = $this->sqlFlights($param, implode(",", $flights) );
                if($flights_scale){
                    array_push($departue_flights_scale, $this->sqlFlights($param, implode(",", $flights) ));
                }
            }
            $distance_direct = $departue_flights[0]["distance"];
            //scale flights 1
            if( count($departue_flights_scale) > 0 && count($departue_flights_scale[0]) > 1 && ( $departue_flights_scale[0][0]["distance"] + $departue_flights_scale[0][1]["distance"] ) <= ($distance_direct + ($distance_direct * 0.30) ) ){
                $departue_flights[3] = $departue_flights_scale[0];
            }
            // scale flights 2
            if( count($departue_flights_scale) > 1 && count($departue_flights_scale[1]) > 1 && ( $departue_flights_scale[1][0]["distance"] + $departue_flights_scale[1][1]["distance"] ) <= ($distance_direct + ($distance_direct * 0.30) ) ){
                $departue_flights[4] = $departue_flights_scale[1];
            }
        }
        // set structure to data and calc prices
        foreach ($departue_flights as $key => &$flights) {
            $total_price = 0;
            if( count($flights) == 2 ){ // flights scale
                foreach ($flights as $keyScale => $scale) {
                    $sub_total_price = $this->calcPrice($scale);
                    $total_price = $total_price + $sub_total_price;
                }
                $total_price = round($total_price - ($total_price * 0.40), 2);
                $flights = ["total_price" => $total_price, "type" => "Stopover flight", "flights" => $flights];
            }else{
                $days = $flights["within_days"] * 1;
                $total_price = $days < 1 ? ($flights["price"] + ($flights["price"] * 0.35)) : $days < 7 ? ($flights["price"] + ($flights["price"] * 0.20)) : $flights["price"];
                $total_price = $this->calcPrice($flights);
                $flights = ["total_price" => $total_price, "type" => "Non-stop flight", "flights" => $flights];
            }
        }

        // return result
        return $departue_flights;
    }

    private function calcPrice($flight) {
        $days = $flight["within_days"] * 1;
        $sub_total_price = $flight["price"] * 1;
        $increment = 1;
        if($days == 0){
            $increment = 0.35;
        }else if($days < 8){
            $increment = 0.20;
        }
        return $increment < 1 ? $sub_total_price + ($sub_total_price * $increment) : $sub_total_price;
    }

    private function sqlFlights($param, $id = null) {

        if($id){

            $is_id = $id ? "flights.id in (".$id.")" : "";

            $sql = "SELECT flights.id, airlines.name as airline, flights.code as flight_number, departure_airp.iata_code as departure_airport, arrival_airp.iata_code as arrival_airport, flights.departure_date, flights.arrival_date, TIMESTAMPDIFF(DAY, \"".$param["check_in"]."\", flights.departure_date ) as within_days, flights.base_price as price, (SELECT kilometers FROM distances WHERE airport_1 = departure_airp.iata_code AND airport_2 = arrival_airp.iata_code) as distance FROM flights LEFT JOIN airplanes ON airplanes.id = flights.airplane_id LEFT JOIN airlines ON airlines.id = airplanes.airline_id LEFT JOIN airports departure_airp ON departure_airp.id = flights.departure_airport_id LEFT JOIN airports arrival_airp ON arrival_airp.id = flights.arrival_airport_id WHERE ".$is_id. " AND (SELECT kilometers FROM distances WHERE airport_1 = departure_airp.iata_code AND airport_2 = arrival_airp.iata_code) < 10000 ORDER BY flights.departure_date ASC";

        }else{

            $departure = "AND departure_airp.iata_code = \"".$param['departure_airport']."\"";
            $arrival = "AND arrival_airp.iata_code = \"".$param['arrival_airport']."\"";

            $sql = "SELECT flights.id, airlines.name as airline, flights.code as flight_number, departure_airp.iata_code as departure_airport, arrival_airp.iata_code as arrival_airport, flights.departure_date, flights.arrival_date, TIMESTAMPDIFF(DAY, \"".$param["check_in"]."\", flights.departure_date ) as within_days, flights.base_price as price, (SELECT kilometers FROM distances WHERE airport_1 = departure_airp.iata_code AND airport_2 = arrival_airp.iata_code) as distance FROM flights LEFT JOIN airplanes ON airplanes.id = flights.airplane_id LEFT JOIN airlines ON airlines.id = airplanes.airline_id LEFT JOIN airports departure_airp ON departure_airp.id = flights.departure_airport_id LEFT JOIN airports arrival_airp ON arrival_airp.id = flights.arrival_airport_id WHERE flights.status = 'scheduled' AND arrival_airp.id != departure_airp.id AND airplanes.first_class_seats > 0 AND flights.departure_date > DATE_ADD(\"".$param['check_in']."\", INTERVAL -1 DAY) ".$departure." ".$arrival." ORDER BY flights.base_price ASC, flights.departure_date ASC LIMIT 5";
        }

        return $this->helpers->aplicateConn( $sql );
    }

}
