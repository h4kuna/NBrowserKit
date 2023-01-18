<?php declare(strict_types=1);

namespace NBrowserKit;

final class Response extends NetteResponseProxy
{

	/**
	 * @var array<string, string>
	 */
	private array $headers = [];


	public function __construct()
	{
		// Intentionally not calling the parent constructor (parent constructor causes side effects).
	}


	public function setHeader(string $name, ?string $value): static
	{
		if ($value === null) {
			unset($this->headers[$name]);
		} else {
			$this->headers[$name] = $value;
		}

		return $this;
	}


	public function addHeader(string $name, string $value): static
	{
		$this->headers[$name] = $value;

		return $this;
	}


	public function deleteHeader(string $name): static
	{
		unset($this->headers[$name]);

		return $this;
	}


	public function getHeader(string $header): ?string
	{
		return $this->headers[$header] ?? null;
	}


	/**
	 * @return array<string, string>
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

}
