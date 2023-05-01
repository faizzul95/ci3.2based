<?php

namespace App\services\general\traits;

if (!defined('BASEPATH')) exit('No direct script access allowed');

trait SecurityRateLimitingTrait
{
	/**
	 * The ip spoofing status check
	 */
	protected $spoofEnabled = true;

	/**
	 * The maximum number of requests allowed per second.
	 */
	protected $requestLimit = 15;

	/**
	 * The maximum cache of requests store before re-generate.
	 */
	protected $cacheDuration = 60;

	/**
	 * The duration for which an IP address should be blocked (in seconds).
	 */
	protected $blockDuration = 60;

	/**
	 * The cache file path to use for storing request counts and blocked IPs.
	 */
	protected $cacheFilePath = APPPATH . 'cache/security_rate_limiting/';

	/**
	 * The cache file extension to use for storing request counts and blocked IPs.
	 */
	protected $cacheFileExtension = '.cache';

	/**
	 * An array of IP addresses to exclude from rate limiting.
	 */
	protected $excludedIPs = [];

	/**
	 * An array of URLs to exclude from rate limiting.
	 */
	protected $excludedURLs = [];

	/**
	 * Checks if the current request should be rate limited and, if so, returns true.
	 *
	 * @return bool
	 */
	protected function isRateLimited()
	{
		$CI = &get_instance();
		$CI->load->config('security');

		// check if throttle is not enabled
		if (!filter_var($CI->config->item('throttle_enable'), FILTER_VALIDATE_BOOLEAN)) {
			return false;
		}

		// load settings exclude URLs & IPs
		$this->excludedURLs = $CI->config->item('throttle_exclude_url'); // define excluded URLs
		$this->excludedIPs = $CI->config->item('throttle_exclude_ips'); // define excluded IPs

		// load settings
		$settings = $CI->config->item('throttle_settings');
		if ($settings) {
			$this->requestLimit = $settings['request']; // set maximum requests per minute
			$this->cacheDuration = $settings['expired']; // set maximum requests per minute
			$this->blockDuration = $settings['block']; // set block time in seconds
		}

		// If the IP address is excluded, do not rate limit
		if ($this->isIPExcluded()) {
			return false;
		}

		// If the URL is excluded, do not rate limit
		if ($this->isURLExcluded()) {
			return false;
		}

		// Check for rate limiting
		if (!$this->isIPBlocked()) {
			if (!$this->isSpoofed()) {
				$cacheFile = $this->getCacheFile();

				// Get the current request count for this IP address
				$requestFile = @file_get_contents($cacheFile);
				$dataRequest = json_decode($requestFile);

				$lastTimestamp = isset($dataRequest->timestamp) ? $dataRequest->timestamp : NULL;
				$requestCount = isset($dataRequest->requestCount) ? (int) ($dataRequest->requestCount + 1) : 1;

				// check if last timestamp has expired
				if ($lastTimestamp) {
					if (time() > ($lastTimestamp + $this->cacheDuration)) {
						@unlink($this->cacheFile);
						$requestCount = 1; // reset count
					}
				}

				// Increment the request count
				@file_put_contents($cacheFile, json_encode([
					'timestamp' => time(),
					'time_request' => timestamp(),
					'requestCount' => $requestCount
				]));

				// If the request count exceeds the limit, rate limit the request
				if ($requestCount > $this->requestLimit) {
					$this->blockIP();
					json(['resCode' => 429, 'message' => 'Too Many Requests']);
				}
			} else {
				$this->blockIP();
				json(['resCode' => 429, 'message' => 'IP addresses do not match, IP has been blocked']);
			}
		} else {
			json(['resCode' => 429, 'message' => 'Too Many Requests']);
		}
	}

	/**
	 * Checks if the current IP address is excluded from rate limiting.
	 *
	 * @return bool
	 */
	protected function isIPExcluded()
	{
		$CI = &get_instance();

		// Get the IP address of the client
		$ip = $CI->input->ip_address();

		// Check if the IP address is in the excluded list
		return in_array($ip, $this->excludedIPs);
	}

	/**
	 * Checks if the current URL is excluded from rate limiting.
	 *
	 * @return bool
	 */
	protected function isURLExcluded()
	{
		$CI = &get_instance();

		// Get the current URL
		// $url = $CI->uri->uri_string();
		$url = rtrim(segment(1) . '/' . segment(2), '/');

		// Check if the URL is in the excluded list
		return in_array($url, $this->excludedURLs);
	}

	/**
	 * Checks if the current request is being spoofed by a different IP address.
	 *
	 * @return bool
	 */
	protected function isSpoofed()
	{
		$CI = &get_instance();

		// Get the IP address of the client
		$clientIP = $CI->input->ip_address();

		// Get the IP address of the server
		if ($this->spoofEnabled && isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$serverIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$serverIP = $_SERVER['REMOTE_ADDR'];
		}

		// If the IP addresses do not match, the request is being spoofed
		if ($clientIP !== $serverIP) {
			return true;
		}

		return false;
	}

	/**
	 * Blocks the current IP address for the specified duration.
	 */
	protected function blockIP()
	{
		$cacheFile = $this->getCacheFile();

		// Delete the current request count
		if ($cacheFile)
			@unlink($cacheFile);

		// Block the IP address
		$blockFile = $this->getBlockFile();
		@file_put_contents($blockFile, json_encode([
			'ip_address' => ci()->input->ip_address(),
			'timestamp' => time(),
			'time_blocked' => timestamp(),
		]));
	}

	/**
	 * Checks if the current IP address is blocked.
	 *
	 * @return bool
	 */
	protected function isIPBlocked()
	{
		$CI = &get_instance();
		$blockFile = $this->getBlockFile();

		// If the block file does not exist, the IP address is not blocked
		if (!file_exists($blockFile)) {
			return false;
		}

		// Get the current request count for this IP address
		$requestFile = @file_get_contents($blockFile);
		$dataBlocked = json_decode($requestFile);

		// Get the timestamp for when the IP address was blocked
		$blockTime = $dataBlocked->timestamp;

		// If the block duration has expired, unblock the IP address
		if (time() > ($blockTime + $this->blockDuration)) {
			@unlink($blockFile);
			return false;
		}

		return true;
	}

	/**
	 * Returns the cache file path for storing the request count.
	 *
	 * @return string
	 */
	protected function getCacheFile()
	{
		$CI = &get_instance();

		// Create the cache directory if it does not exist
		if (!is_dir($this->cacheFilePath)) {
			mkdir($this->cacheFilePath, 0755, true);
		}

		// Get the cache file path for the current IP address
		$ip = $CI->input->ip_address();
		return $this->cacheFilePath . md5($ip) . $this->cacheFileExtension;
	}

	/**
	 * Returns the file path for storing blocked IP addresses.
	 *
	 * @return string
	 */
	protected function getBlockFile()
	{
		$CI = &get_instance();

		// Create the cache directory if it does not exist
		if (!is_dir($this->cacheFilePath)) {
			mkdir($this->cacheFilePath, 0755, true);
		}

		// Get the file path for the blocked IP address list
		$ip = $CI->input->ip_address();
		return $this->cacheFilePath . md5($ip) . '_blocked' . $this->cacheFileExtension;
	}
}
