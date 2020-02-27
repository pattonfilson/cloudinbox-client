<?php

namespace cloudsystems\cloudinbox;

use GuzzleHttp\Client as HttpClient;


class CloudInbox
{


	use MakesHttpRequests,
		actions\ManagesMessages;


	/**
	 * The mailbox name
	 *
	 * @var string
	 *
	 */
	public $apiKey;


	/**
	 * The mailbox key
	 *
	 * @var string
	 *
	 */
	public $apiSecret;


	/**
	 * The Guzzle HTTP Client instance.
	 *
	 * @var \GuzzleHttp\Client
	 *
	 */
	public $guzzle;


	/**
	 * Number of seconds a request is retried.
	 *
	 * @var int
	 *
	 */
	public $timeout = 30;


	/**
	 * Create a new RunCloud instance.
	 *
	 * @param  string $apiKey
	 * @param  string $apiSecret
	 * @param  \GuzzleHttp\Client $guzzle
	 * @return void
	 *
	 */
	public function __construct(string $apiKey, string $apiSecret, string $uri = null, HttpClient $guzzle = null)
	{
		$this->apiKey = $apiKey;
		$this->apiSecret = $apiSecret;

		$baseUri = $uri ?: 'https://cloudinbox.clouddataservice.co.uk/';

		$this->guzzle = $guzzle ?: new HttpClient([
			'base_uri' => $baseUri,
			'auth' => [$this->apiKey, $this->apiSecret],
			'headers' => [
			  'Content-Type' => 'application/json',
			  'Accept' => 'application/json',
			],
			'allow_redirects' => false
		]);
	}


	/**
	 * Transform the items of the collection to the given class.
	 *
	 * @return array
	 *
	 */
	public function transformCollection(array $collection, string $class, array $extraData = []): array
	{
		return array_map(function ($data) use ($class, $extraData) {
			return new $class($data + $extraData, $this);
		}, $collection);
	}


	/**
	 * Transform the item to the given class.
	 *
	 * @return object
	 *
	 */
	public function transformItem(array $item, string $class, array $extraData = []): object
	{
		return new $class($item + $extraData, $this);
	}


	protected function getAllItems(string $path, array $query = [])
	{
		$result = $this->get($path, $query);
		$itemsAll = $result['items'];

		if (array_key_exists('_meta', $result)) {

			$page = $result['_meta']['currentPage'];
			$pageCount = $result['_meta']['pageCount'];

			 while ($page < $pageCount) {
				$page++;
				$pageQuery = array_merge($query, ['page' => $page]);
				$items = $this->get($path, $pageQuery)['items'];
				$itemsAll = array_merge($itemsAll, $items);
			}

		}

		return $itemsAll;
	}


	/**
	 * Set a new timeout
	 *
	 * @param  int $timeout
	 * @return $this
	 *
	 */
	public function setTimeout(int $timeout)
	{
		$this->timeout = $timeout;
		return $this;
	}


	/**
	 * Get the timeout
	 *
	 * @return  int
	 *
	 */
	public function getTimeout(): int
	{
		return $this->timeout;
	}


	public function ping()
	{
		return $this->get("ping")['message'];
	}


}
