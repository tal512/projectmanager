<?php

class Validator
{
	public static function booleanInteger($value) {
		return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[0-1]{1}$/']]);
	}

	public static function email($value)
	{
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}

	public static function hexadecimal($value, $allowEmpty = true)
	{
		return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-f0-9]*$/']]);
	}

	public static function integer($value, $allowEmpty = false)
	{
		if ($allowEmpty === true) {
			return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[0-9]+$/']]) || $value === null;
		} else {
			return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[0-9]+$/']]);
		}
	}

	public static function password($value)
	{
		$password = password_get_info($value);
		return ($password['algo'] !== 0) ? true : false;
	}

	public static function safe($value)
	{
		return $value;
	}

	public static function string($value)
	{
		return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	}
}