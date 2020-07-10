<?php


namespace App\EventListener;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use function get_class;
use function time;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        $data = null;

        if ($exception instanceof HttpExceptionInterface) {
            $data = [
                'class' => get_class($exception),
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