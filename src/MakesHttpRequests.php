<?php

namespace cloudsystems\cloudinbox;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;
use cloudsystems\cloudinbox\exceptions\UnwantedRedirectException;
use cloudsystems\cloudinbox\exceptions\AuthenticationFailedException;
use cloudsystems\cloudinbox\exceptions\ForbiddenRequestException;
use cloudsystems\cloudinbox\exceptions\NotFoundException;
use cloudsystems\cloudinbox\exceptions\ValidationException;
use cloudsystems\cloudinbox\exceptions\TooManyRequestsException;
use cloudsystems\cloudinbox\exceptions\TimeoutException;


trait MakesHttpRequests
{


	/**
	 * Make a GET request and return the response.
	 *
	 * @param  string $uri
	 * @return mixed
	 *
	 */
	private function get($uri, $params = [])
	{
		return $this->request('GET', $uri, $params);
	}


	/**
	 * Make a POST request and return the response.
	 *
	 * @return mixed
	 *
	 */
	private function post(string $uri, array $payload = [])
	{
		return $this->request('POST', $uri, $payload);
	}


	/**
	 * Make a PUT request and return the response.
	 *
	 * @return mixed
	 *
	 */
	private function put(string $uri, array $payload = [])
	{
		return $this->request('PUT', $uri, $payload);
	}


	/**
	 * Make a PATCH request and return the response.
	 *
	 * @return mixed
	 *
	 */
	private function patch(string $uri, array $payload = [])
	{
		return $this->request('PATCH', $uri, $payload);
	}


	/**
	 * Make a DELETE request and return the response.
	 *
	 * @return mixed
	 *
	 */
	private function delete(string $uri, array $payload = [])
	{
		return $this->request('DELETE', $uri, $payload);
	}


	/**
	 * Make request and return the response.
	 *
	 * @return mixed
	 *
	 */
	private function request(string $verb, string $uri, array $params = [])
	{
		$options = [];

		if ( ! empty($params)) {
			if ($verb === 'GET') {
				$options['query'] = $params;
			} else {
				$options['form_params'] = $params;
			}
		}

		try {
			$response = $this->guzzle->request($verb, $uri, $options);
		} catch (ClientException $e) {
			return $this->handleRequestError($e->getResponse());
		}

		$responseBody = (string) $response->getBody();

		return json_decode($responseBody, true) ?: $responseBody;
	}


	/**
	 * @param  \Psr\Http\Message\ResponseInterface $response
	 * @return void
	 *
	 */
	private function handleRequestError(ResponseInterface $response)
	{
		if ($response->getStatusCode() == 302) {
			throw new UnwantedRedirectException();
		}

		if ($response->getStatusCode() == 401) {
			throw new AuthenticationFailedException();
		}

		if ($response->getStatusCode() == 403) {
			throw new ForbiddenRequestException();
		}

		if ($response->getStatusCode() == 404) {
			throw new NotFoundException();
		}

		if ($response->getStatusCode() == 422) {
			throw new ValidationException(json_decode((string) $response->getBody(), true));
		}

		if ($response->getStatusCode() == 429) {
			throw new TooManyRequestsException();
		}

		throw new \Exception((string) $response->getBody());
	}


	/**
	 * Retry the callback or fail after x seconds.
	 *
	 * @return mixed
	 *
	 */
	public function retry(int $timeout, Callable $callback)
	{
		$start = time();

		beginning:

		if ($output = $callback()) {
			return $output;
		}

		if (time() - $start < $timeout) {
			sleep(5);

			goto beginning;
		}

		throw new TimeoutException($output);
	}


}
