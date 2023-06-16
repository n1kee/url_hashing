<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Validator\Validator;

class Controller extends AbstractController
{
    public function validate(array $data, Validator $validator) {

        $validator->populate($data);

        $errors = $validator->validate();

        if ($errors) $this->sendErrorResponse(json_encode($errors));
    }

    /**
     */
    protected function sendErrorResponse(string $msg, $httpCode = 400): JsonResponse
    {
        $response = $this->json([
            'error' => $msg,
        ], $httpCode);
        $response->send();
        exit;
    }
}
