<?php

namespace App\Service;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Helpers {

    public $params;
    public $manager;
    public $entitySpaceName;

    public function __construct($manager, ContainerBagInterface $params) {
        $this->params = $params;
        $this->manager = $manager;
        $this->entitySpaceName = 'App\Entity\\';
    }

    private function getHttpStatusCode($code) { // https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
        switch ($code) {
            case 511:
                return Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED;
            case 510:
                return Response::HTTP_NOT_EXTENDED;
            case 508:
                return Response::HTTP_LOOP_DETECTED;
            case 507:
                return Response::HTTP_INSUFFICIENT_STORAGE;
            case 506:
                return Response::HTTP_VARIANT_ALSO_NEGOTIATES;
            case 505:
                return Response::HTTP_VERSION_NOT_SUPPORTED;
            case 504:
                return Response::HTTP_GATEWAY_TIMEOUT;
            case 503:
                return Response::HTTP_SERVICE_UNAVAILABLE;
            case 502:
                return Response::HTTP_BAD_GATEWAY;
            case 501:
                return Response::HTTP_NOT_IMPLEMENTED;
            case 500:
                return Response::HTTP_INTERNAL_SERVER_ERROR;
            case 451:
                return Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS;
            case 431:
                return Response::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE;
            case 429:
                return Response::HTTP_TOO_MANY_REQUESTS;
            case 428:
                return Response::HTTP_PRECONDITION_REQUIRED;
            case 426:
                return Response::HTTP_UPGRADE_REQUIRED;
            case 425:
                return Response::HTTP_TOO_EARLY;
            case 424:
                return Response::HTTP_FAILED_DEPENDENCY;
            case 423:
                return Response::HTTP_LOCKED;
            case 422:
                return Response::HTTP_UNPROCESSABLE_ENTITY;
            case 421:
                return Response::HTTP_MISDIRECTED_REQUEST;
            case 417:
                return Response::HTTP_EXPECTATION_FAILED;
            case 416:
                return Response::HTTP_RANGE_NOT_SATISFIABLE;
            case 415:
                return Response::HTTP_UNSUPPORTED_MEDIA_TYPE;
            case 414:
                return Response::HTTP_URI_TOO_LONG;
            case 413:
                return Response::HTTP_PAYLOAD_TOO_LARGE;
            case 412:
                return Response::HTTP_PRECONDITION_FAILED;
            case 411:
                return Response::HTTP_LENGTH_REQUIRED;
            case 410:
                return Response::HTTP_GONE;
            case 409:
                return Response::HTTP_CONFLICT;
            case 408:
                return Response::HTTP_REQUEST_TIMEOUT;
            case 407:
                return Response::HTTP_PROXY_AUTHENTICATION_REQUIRED;
            case 406:
                return Response::HTTP_NOT_ACCEPTABLE;
            case 405:
                return Response::HTTP_METHOD_NOT_ALLOWED;
            case 404:
                return Response::HTTP_NOT_FOUND;
            case 403:
                return Response::HTTP_FORBIDDEN;
            case 402:
                return Response::HTTP_PAYMENT_REQUIRED;
            case 401:
                return Response::HTTP_UNAUTHORIZED;
            case 400:
                return Response::HTTP_BAD_REQUEST;
            case 308:
                return Response::HTTP_PERMANENT_REDIRECT;
            case 307:
                return Response::HTTP_TEMPORARY_REDIRECT;
            case 306:
                return Response::HTTP_SWITCH_PROXY;
            case 305:
                return Response::HTTP_USE_PROXY;
            case 304:
                return Response::HTTP_NOT_MODIFIED;
            case 303:
                return Response::HTTP_SEE_OTHER;
            case 302:
                return Response::HTTP_FOUND;
            case 301:
                return Response::HTTP_MOVED_PERMANENTLY;
            case 300:
                return Response::HTTP_MULTIPLE_CHOICES;
            case 226:
                return Response::HTTP_IM_USED;
            case 208:
                return Response::HTTP_ALREADY_REPORTED;
            case 206:
                return Response::HTTP_PARTIAL_CONTENT;
            case 205:
                return Response::HTTP_RESET_CONTENT;
            case 204:
                return Response::HTTP_NO_CONTENT;
            case 202:
                return Response::HTTP_ACCEPTED;
            case 201:
                return Response::HTTP_CREATED;
            case 103:
                return Response::HTTP_EARLY_HINTS;
            case 102:
                return Response::HTTP_PROCESSING;
            case 101:
                return Response::HTTP_SWITCHING_PROTOCOLS;
            case 100:
                return Response::HTTP_CONTINUE;
            default:
                return Response::HTTP_OK; // 200
        }
    }

    public function json($data, $status = null) {

        $encoders = new JsonEncoder();
        $normalizers = new GetSetMethodNormalizer();
        $serializer = new Serializer(array(
            new DateTimeNormalizer(), $normalizers), array($encoders));
        $json = $serializer->serialize($data, 'json');
        $response = new Response;
        $response->setContent(utf8_encode($json));
        $response->setStatusCode($this->getHttpStatusCode($status));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function aplicateConn($query, $parameters = [], $delete = false, $debug = false) {

        $conn = $this->manager->getConnection();
        if($debug){
            $stack = new \Doctrine\DBAL\Logging\DebugStack();
            $conn->getConfiguration()->setSQLLogger($stack);
        }
        $stmt = $conn->prepare($query);
        $stmt->execute($parameters);
        // dont remove the next line atte. ISAAC MENDOZA
        if($debug){ var_dump($stack->queries);die; }

        return $delete ? true : $stmt->fetchAll();
    }

}