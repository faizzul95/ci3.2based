<?php

namespace App\services\general\traits;

if (!defined('BASEPATH')) exit('No direct script access allowed');

trait SecurityRateLimittingTrait
{
	public function check_rate_limit()
	{
		$CI = &get_instance();
		$CI->load->driver('cache');

		$CI->load->config('security');
		$isEnable = $CI->config->item('throttle_enable');

		// check if system is not in maintenance & rate limitting is enable
		if (!checkMaintenance() && filter_var($isEnable, FILTER_VALIDATE_BOOLEAN)) {

			$settings = $CI->config->item('throttle_settings');
			$max_requests = $settings['request']; //set maximum requests per minute
			$expiration_time = $settings['expired']; //set expiration time in seconds
			$max_reminder_count = $settings['reminder']; //set maximum reminder before block

			$excluded_urls = $CI->config->item('throttle_exclude_url'); // define excluded URLs

			// prevent IP spoofing by getting the IP address from trusted headers
			$trusted_headers = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP');
			$ip_address = NULL;
			foreach ($trusted_headers as $header) {
				if ($CI->input->server($header) !== false) {
					$ip_address = $CI->input->server($header);
					break;
				}
			}
			$ip_address = $ip_address ? $ip_address : $CI->input->ip_address(); // fallback to default IP address
			$ip_address = str_replace(".", "_", $ip_address);

			// prevent XSS attacks by sanitizing the requested URL
			// $url = $CI->security->xss_clean($CI->input->server('REQUEST_URI'));
			$url = rtrim(segment(1) . '/' . segment(2), '/');

			if (!in_array($url, $excluded_urls)) {

				$cache_name = 'rate_limit_' . $ip_address;
				$request_count = $CI->cache->get($cache_name);
				$request_count = ($request_count) ? $request_count : 0;

				$current_time = time();
				$cache_data = array(
					'timestamp' => $current_time,
					'count' => ($request_count + 1)
				);

				$CI->cache->save($cache_name, $cache_data, $expiration_time); // save current request count in cache

				if (($request_count + 1) > $max_requests) {
					// if exceeded maximum requests per minute, block IP
					$block_time = $settings['block']; // set block time in seconds (24 hours)
					$block_cache_name = 'block_ip_' . $ip_address;
					$block_cache_data = array(
						'timestamp' => $current_time,
						'count' => 1
					);
					$CI->cache->save($block_cache_name, $block_cache_data, $block_time); // save blocked IP in cache
					// show_error('Rate limit exceeded. Please try again later.'); // show error message to the user
					$this->returnResponse(['resCode' => 429, 'message' => 'Too Many Requests']);
				}

				$block_cache_name = 'block_ip_' . $ip_address;
				$block_cache_data = $CI->cache->get($block_cache_name);

				if ($block_cache_data) {
					// if IP is blocked, check block count
					$block_count = $block_cache_data['count'];
					if ($block_count >= $max_reminder_count) {
						// show_error('You are blocked for 24 hours due to excessive requests.'); //show error message to the user
						$this->returnResponse(['resCode' => 403, 'message' => 'You are blocked for 24 hours due to excessive requests']);
					} else {
						//increment block count and save in cache
						$block_count++;
						$block_cache_data = array(
							'timestamp' => $current_time,
							'count' => $block_count
						);
						$CI->cache->save($block_cache_name, $block_cache_data, $block_time);
						// show_error('Rate limit exceeded. Please try again later.'); // show error message to the user
						$this->returnResponse(['resCode' => 429, 'message' => 'Too Many Requests']);
					}
				}
			}
		}
	}

	public function returnResponse($response)
	{
		json($response);
	}
}
