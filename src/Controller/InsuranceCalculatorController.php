<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\InsuranceCalculationResponseDTO;
use App\Errors\ErrorResponse;
use App\Model\InsuranceCalculationRequest;
use App\Service\InsuranceCalculatorService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InsuranceCalculatorController extends AbstractController
{
    private InsuranceCalculatorService $calculatorService;
    private ValidatorInterface $validator;

    public function __construct(InsuranceCalculatorService $calculatorService, ValidatorInterface $validator)
    {
        $this->calculatorService = $calculatorService;
        $this->validator = $validator;
    }

    #[Route('/api/insurance/calculate', name: 'insurance_calculate', methods: ['POST'])]
    #[OA\Post(
        description: 'Расчет стоимости страховки для выезжающих за рубеж на основе страховой суммы, дат поездки и валюты',
        summary: 'Расчет стоимости страховки',
        tags: ['Insurance']
    )]
    #[OA\RequestBody(
        description: 'Данные для расчета страховки',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: InsuranceCalculationRequest::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Успешный расчет',
        content: new OA\JsonContent(
            ref: new Model(type: InsuranceCalculationResponseDTO::class)
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Неверные входные данные',
        content: new OA\JsonContent(
            ref: new Model(type: ErrorResponse::class)
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Внутренняя ошибка сервера',
        content: new OA\JsonContent(
            ref: new Model(type: ErrorResponse::class)
        )
    )]
    public function calculate(Request $request): JsonResponse
    {
        try {
            $content = $request->getContent();

            if (empty($content)) {
                return $this->json(['error' => 'Empty request body'], 400);
            }

            if (!$this->isValidJson($content)) {
                return $this->json(['error' => 'Invalid JSON'], 400);
            }

            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($data)) {
                return $this->json(['error' => 'JSON must be an object'], 400);
            }

            $constraints = new Assert\Collection(
                [
                    'insuranceAmount' => [new Assert\NotBlank(), new Assert\Type('int')],
                    'startDate' => [new Assert\NotBlank(), new Assert\Date()],
                    'endDate' => [new Assert\NotBlank(), new Assert\Date()],
                    'currencyCode' => [new Assert\NotBlank(), new Assert\Length(3)],
                ]
            );

            $violations = $this->validator->validate($data, $constraints);

            if ($violations->count() > 0) {
                $errors = [];

                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }

                return $this->json(['errors' => $errors], 400);
            }

            $calculationRequest = new InsuranceCalculationRequest(
                (int) $data['insuranceAmount'],
                $data['startDate'],
                $data['endDate'],
                strtoupper($data['currencyCode'])
            );

            $result = $this->calculatorService->calculate($calculationRequest);

            return $this->json($result->toArray());

        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], 500);
        }
    }

    private function isValidJson(string $string): bool
    {
        if (function_exists('json_validate')) {
            return json_validate($string);
        }

        json_decode($string);

        return JSON_ERROR_NONE === json_last_error();
    }
}
