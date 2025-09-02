<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\InsuranceCalculationRequest;
use App\Service\InsuranceCalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
	public function calculate(Request $request): JsonResponse
	{
		try {
			$data = json_decode(
				$request->getContent(),
				TRUE,
				512,
				JSON_THROW_ON_ERROR);

			if (json_last_error() !== JSON_ERROR_NONE) {
				return $this->json(['error' => 'Invalid JSON'], 400);
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
				$data['insuranceAmount'],
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
}