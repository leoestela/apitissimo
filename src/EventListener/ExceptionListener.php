<?php


namespace App\EventListener;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use function time;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        $data = [];

        if ($exception instanceof HttpExceptionInterface) {
            $data = [
                'message' => $exception->getMessage(),
                'code' => $exception->getStatusCode()
            ];
        } else {
            $data = [
                'message' => 'Internal server error',
                'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ];
        }

        $event->setResponse($this->getJsonResponse($data));
    }

    public function getJsonResponse(array $data):JsonResponse
    {
        $jsonResponse = new JsonResponse($data, $data['code']);
        $jsonResponse->headers->set('Server-Time', time());
        $jsonResponse->headers->set('X-Error-Code', $data['code']);
        $jsonResponse->headers->set('Content-Type', 'application/json');

        return $jsonResponse;
    }
}