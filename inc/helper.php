<?php 

/**
 * Gets client IP.
 * 
 * Warning: This is naive and easily spoofed by malicious clients. Don't do any security relevant stuff with it...
 * 
 * @return string
 */
function ipr_get_client_ip() {

	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	    $ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
	    $ip = $_SERVER['REMOTE_ADDR'];
	}
	
	return $ip;
}

function ipr_get_allowed_ips() {
	$ips = get_site_option('ipr_network_option_ipranges', false);

	if ($ips === false) {
		return [];
	}

	$ips = explode("\n", $ips);
	$ips = array_map(function($ip) { return trim($ip); }, $ips);

	return $ips;
}

/**
 * Check if client is in configured IPs / IP ranges.
 * 
 * @return boolean
 */
function ipr_client_ip_is_allowed() {
	$client_ip   = ipr_get_client_ip();
	$allowed_ips = ipr_get_allowed_ips();

	$pattern_to_regex = function($p) {
		$p = preg_replace_callback("|\[(\d+)-(\d+)\]|", function($matches) {
			return "(" . implode("|", range($matches[1], $matches[2])) . ")";
		}, $p); // allow ranges like [0-1] or [33-60]
		$p = str_replace(".", "\.", $p); // turn dots into real dots
		$p = str_replace("*", "\d{1,3}", $p); // wildcard to "1 to 3 digit number"
		$p = '/^' . $p . '$/'; // make sure it's a complete match
		return $p;
	};


	foreach ($allowed_ips as $ip) {
		
		if ($client_ip == $ip) {
			return true;
		}

		if (preg_match($pattern_to_regex($ip), $client_ip)) {
			return true;
		}

	}

	return false;
}

/**
 * Check if post is restricted
 * 
 * @param  $postid Post ID to test against
 * @return boolean
 */
function ipr_is_post_restricted( $postid ){

	$is_restricted = get_post_meta(get_the_id(), 'hide-post-checkbox')[0];
	
	return $is_restricted == 'yes';
}
