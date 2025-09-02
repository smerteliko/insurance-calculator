<?php
declare(strict_types=1);

namespace App\Errors;

use OpenApi\Attributes as OA;

#[OA\Schema(
	schema: "ErrorResponse",
	description: "Ответ с ошибкой"
)]
class ErrorResponse
{
	#[OA\Property(description: "Сообщение об ошибке", example: "Invalid insurance amount")]
	private ?string $error = null;

	#[OA\Property(
		description: "Детальные ошибки валидации",
		type: "object",
		example: [ "insuranceAmount" => "This value should be of type int."],
		additionalProperties: new OA\Property(type: "string")
	)]
	private ?array $errors = null;

	public function __construct(?string $error = null, ?array $errors = null)
	{
		$this->error = $error;
		$this->errors = $errors;
	}

	public function getError(): ?string
	{
		return $this->error;
	}

	public function getErrors(): ?array
	{
		return $this->errors;
	}
}