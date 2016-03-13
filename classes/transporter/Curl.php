<?php

namespace transporter;

use classes\AppConfig;
use response\Json;

class Curl extends \transporter\Transporter
{
	protected $certificate;

	public function __construct($xtoken = null)
	{
		parent::__construct($xtoken);

		$this->certificate = BASE_DIR . '/config/cacert.pem';
	}

	public function get($url)
	{
		return $this->request($url, 'GET');
	}

	public function post($url, $data)
	{;
		return $this->request($url, 'POST', $data);
	}

	public function request($url, $method, $data = null, $options = array(), $headers = array())
	{
		$ch = curl_init();

		$options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);

		$options[CURLOPT_URL] = (string) $url;

		if (isset($data))
		{
			// If the data is a scalar value simply add it to the cURL post fields.
			if (is_scalar($data) || (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'multipart/form-data') === 0))
			{
				$options[CURLOPT_POSTFIELDS] = $data;
			}
			// Otherwise we need to encode the value first.
			else
			{
				$options[CURLOPT_POSTFIELDS] = http_build_query($data);
			}

			if (!isset($headers['Content-Type']))
			{
				$headers['Content-Type'] = 'application/json';
			}

			// Add the relevant headers.
			if (is_scalar($options[CURLOPT_POSTFIELDS]))
			{
				$headers['Content-Length'] = strlen($options[CURLOPT_POSTFIELDS]);
			}
		}

		$headers['x-token'] = $this->xtoken;

		$finishedHeaders = array();

		foreach ($headers as $key => $header)
		{
			$finishedHeaders[] = $key . ': ' . $header;
		}

		$options[CURLOPT_HTTPHEADER] = $finishedHeaders;
		$options[CURLOPT_RETURNTRANSFER] = true;

		if (false && $this->certificate)
		{
			$options[CURLOPT_CAINFO] = $this->certificate;
		}
		else
		{
			$options[CURLOPT_SSL_VERIFYPEER] = false;
		}

		curl_setopt_array($ch, $options);
echo 'Called: ' . $url . "\n";
		$content = curl_exec($ch);

		if ($content == false)
		{
			$errors = curl_error($ch);

			print_r($errors);
		}

		return new \response\JSON($content);
	}
}