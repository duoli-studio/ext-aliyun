<?php namespace Order\Contracts;

interface Platform {

	function publish();

	function getError();

	function setError($error);

	function syncDetail();

	function pubCancel($flag, $pub_pay, $sd_pay, $reason = '');

	function rePublish();

	function delete();

	// function getReqResp();

	// function getReqLog();

	function getStatus();
}