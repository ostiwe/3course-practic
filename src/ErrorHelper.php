<?php


namespace App;


class ErrorHelper
{

	const NOT_VALID_REQUEST_CONTENT_TYPE = 1;
	const AUTH_FAILED_TOKEN = 2;
	const AUTH_FAILED_PASSWORD = 3;
	const AUTH_FAILED_TOKEN_NOT_FOUND = 4;
	const AUTH_FAILED_NOT_PERMISSION = 5;
	const USER_NOT_FOUND = 6;
	const ACCESS_TOKEN_GENERATE_ERROR = 7;
	const REQUEST_WRONG_PARAMS = 8;
	const REGISTER_USER_ALREADY_EXIST = 9;
	const INVALID_REQUEST = 10;
	const POST_NOT_FOUND = 11;
	const FILE_TYPE_NOT_ALLOWED = 12;
	const UPLOAD_ERROR = 13;
	const WORKSHOP_NOT_FOUND = 14;
	const WORKSHOP_ALREADY_SET = 15;
	const NOTHING_CHANGE = 16;
	const MODEL_NOT_FOUND = 17;
	const AUTO_NOT_FOUND = 18;

	public static function notValidRequestContentType(string $needType): array
	{
		return [
			'error' => true,
			'code' => self::NOT_VALID_REQUEST_CONTENT_TYPE,
			'message' => "request content-type must be a $needType",
		];
	}

	public static function authorizationFailed(int $authorizationType): array
	{
		return [
			'error' => true,
			'code' => $authorizationType,
			'message' => 'authorization failed',
		];
	}

	public static function userNotFound(): array
	{
		return [
			'error' => true,
			'code' => self::USER_NOT_FOUND,
			'message' => 'user not found',
		];
	}

	public static function accessTokenGenerateError(): array
	{
		return [
			'error' => true,
			'code' => self::ACCESS_TOKEN_GENERATE_ERROR,
			'message' => 'unable to create access token, try again later',
		];
	}

	public static function requestWrongParams(array $messages): array
	{
		return [
			'error' => true,
			'code' => self::REQUEST_WRONG_PARAMS,
			'message' => 'one or more parameters passed incorrectly',
			'data' => $messages,
		];
	}

	public static function registerError($type): array
	{
		return [
			'error' => true,
			'code' => $type,
			'message' => 'user already exist',
		];
	}

	public static function invalidRequest(): array
	{
		return [
			'error' => true,
			'code' => self::INVALID_REQUEST,
			'message' => 'invalid request',
		];
	}

	public static function postNotFound(): array
	{
		return [
			'error' => true,
			'code' => self::POST_NOT_FOUND,
			'message' => 'post not found',
		];
	}

	public static function noAllowedFileType() : array
	{
		return [
			'error' => true,
			'code' => self::FILE_TYPE_NOT_ALLOWED,
			'message' => 'this file type is not allowed',
		];
	}

	public static function uploadError() : array
	{
		return [
			'error' => true,
			'code' => self::UPLOAD_ERROR,
			'message' => 'file cannot be uploaded now',
		];
	}

	public static function workshopNotFound() : array
	{
		return [
			'error' => true,
			'code' => self::WORKSHOP_NOT_FOUND,
			'message' => 'workshop not found',
		];
	}

	public static function workshopAlreadySet() : array
	{
		return [
			'error' => true,
			'code' => self::WORKSHOP_ALREADY_SET,
			'message' => 'workshop already set',
		];
	}

	public static function nothingChange() : array
	{
		return [
			'error' => true,
			'code' => self::NOTHING_CHANGE,
			'message' => 'nothing change',
		];
	}

	public static function modelNotFound() : array
	{
		return [
			'error' => true,
			'code' => self::MODEL_NOT_FOUND,
			'message' => 'model not found',
		];
	}

	public static function autoNotFound() : array
	{
		return [
			'error' => true,
			'code' => self::AUTO_NOT_FOUND,
			'message' => 'auto not found',
		];
	}
}
