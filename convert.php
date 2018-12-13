<?php

function decode_server_remarks($remark)
{
	return base64_decode(strtr($remark, '-_', '+/'));
}

$file = $argv[1];
$file = file_get_contents($file);
$decode_file = base64_decode($file);
$links2arr = explode("\n", $decode_file);
$decode_links = array();
$ss_links = array();

foreach($links2arr as $key => $link) {
	if (empty($link)) continue;

	// LINK TYPE

	if (strpos($link, "_") !== false) {

		// SSR LINK DECODE

		$link = str_replace("ssr://", "", $link);
		$link_part = explode("_", $link);
		$d_link = base64_decode($link_part[0]);
		$link_parms = explode(":", $d_link);
		preg_match("/(?<=(\&remarks\=)).*.(?=(\&group\=))/", base64_decode($link_part[1]) , $link_parms_ssr);
		$remark = decode_server_remarks($link_parms_ssr[0]);
		$addr = $link_parms[0];
		$port = @$link_parms[1];
		$encrypt_method = @$link_parms[3];
		$password = base64_decode(str_replace("/", "", @$link_parms[5]));

		// ENCODE TO SS

		$ss_start = "ss://";
		$ss_parms = base64_encode($encrypt_method . ":" . $password . "@" . $addr . ":" . $port);
		$ss_remark = urlencode($remark);
		$ss_link = $ss_start . $ss_parms . "#" . $ss_remark;
		array_push($ss_links, $ss_link);
	}
	else {

		// SSR LINK DECODE

		$link = str_replace("ssr://", "", $link);
		$d_link = base64_decode($link);
		$link_parms = explode(":", $d_link);
		preg_match("/(?<=(\&remarks\=)).*.(?=(\&group\=))/", $d_link, $link_parms_ssr);
		$remark = decode_server_remarks($link_parms_ssr[0]);
		$addr = $link_parms[0];
		$port = @$link_parms[1];
		$encrypt_method = @$link_parms[3];
		$password = base64_decode(explode("/?", @$link_parms[5]) [0]);

		// ENCODE TO SS

		$ss_start = "ss://";
		$ss_parms = base64_encode($encrypt_method . ":" . $password . "@" . $addr . ":" . $port);
		$ss_remark = urlencode($remark);
		$ss_link = $ss_start . $ss_parms . "#" . $ss_remark;
		array_push($ss_links, $ss_link);
	}
}

echo "PLEASE COPY THEM AND IMPORT : \n \n \n ";

foreach($ss_links as $l) {
	echo $l . "\n";
}