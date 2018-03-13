<?php namespace Slt\Classes\Contracts;


interface Creator
{
	public function creatorFail($error);

	public function creatorSuccess($model);
}