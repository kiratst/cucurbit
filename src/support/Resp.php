<?php

namespace Cucurbit\Framework\Support;

class Resp
{
	const SUCCESS = 0;
	const ERROR   = 1;

	private $codeTexts = [
		'0' => 'error',
		'1' => 'success',
	];

	/** @var int $code */
	public $code;

	/** @var string $message */
	private $message = '';

	public function __construct($code = null, $message = '')
	{
		$this->setCode($code);
		$this->setMessage($message);
	}

	/**
	 * @param int $code
	 * @return self
	 */
	public function setCode($code)
	{
		if (!$code) {
			$code = self::SUCCESS;
		}

		$this->code = (int) $code;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getCode(): int
	{
		return $this->code;
	}

	/**
	 * @param string $message
	 * @return self
	 */
	public function setMessage($message = '')
	{
		if (!$message && isset($this->codeTexts[$this->code])) {
			$message = $this->codeTexts[$this->code];
		}

		$this->message = $message;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * response
	 * @param int        $code    code
	 * @param null|self  $message message
	 * @param null|array $data    data
	 * @return \Illuminate\Http\JsonResponse
	 */
	public static function response($code, $message = null, $data = null): \Illuminate\Http\JsonResponse
	{
		if (!$message instanceof self) {
			$message = new self($code, $message);
		}

		/** @var self $self */
		$self = $message;

		$return = [
			'status'  => $self->getCode(),
			'message' => $self->getMessage(),
		];

		if ($data) {
			$return['data'] = (array) $data;
		}

		return \Response::json($return, 200, JSON_UNESCAPED_UNICODE);
	}

	/**
	 * success response
	 * @param null $message
	 * @param null $data
	 * @return \Illuminate\Http\JsonResponse
	 */
	public static function success($message = null, $data = null): \Illuminate\Http\JsonResponse
	{
		return self::response(self::SUCCESS, $message, $data);
	}

	/**
	 * error response
	 * @param null $message
	 * @return \Illuminate\Http\JsonResponse
	 */
	public static function error($message = null): \Illuminate\Http\JsonResponse
	{
		return self::response(self::ERROR, $message);
	}
}