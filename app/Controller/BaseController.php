<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\App\Controller;

use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
use W7\Core\Controller\ControllerAbstract;
use W7\Core\Exception\ValidatorException;
use W7\Http\Message\Server\Request;

class BaseController extends ControllerAbstract
{
	public function data($data = [], $message = 'ok', $code = 200)
	{
		return [
			'status' => true,
			'code' => $code,
			'data' => $data,
			'message' => $message,
		];
	}

	public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
	{
		if (empty($request)) {
			throw new ValidatorException('Request object not found');
		}
		$requestData = array_merge([], $request->getQueryParams(), $request->post(), $request->getUploadedFiles());
		return $this->validateMsg($requestData, $rules, $messages, $customAttributes);
	}

	protected function validateMsg(array $data, array $rules, array $messages = [], array $customAttributes = [])
	{
		try {
			/**
			 * @var Factory $validate
			 */
			$validate = ivalidator();
			$result = $validate->make($data, $rules, $messages, $customAttributes)
				->validate();
		} catch (ValidationException $e) {
			$errorMessage = [];
			$errors = $e->errors();
			foreach ($errors as $field => $message) {
				$errorMessage[] = str_replace(['。', ' '], '', $message[0]);
			}
			//反馈去重
			$errorMessage = array_unique($errorMessage);
			throw new ValidatorException(implode('；', $errorMessage), 403);
		}

		return $result;
	}
}
