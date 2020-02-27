<?php

namespace cloudsystems\cloudinbox\resources;

use cloudsystems\cloudinbox\Client;


class Resource
{


	/**
	 * The resource attributes.
	 *
	 * @var array
	 *
	 */
	public $attributes;


	/**
	 * The CloudInbox client instance.
	 *
	 * @var Client
	 *
	 */
	protected $cloudinbox;


	/**
	 * Which properties of this resource should be transformed into collections of their own.
	 *
	 * @var Resource[]
	 *
	 */
	protected $collections = [];


	/**
	 * Create a new resource instance.
	 *
	 * @param  array $attributes
	 * @param  Client $cloudinbox
	 * @return void
	 *
	 */
	public function __construct(array $attributes, $cloudinbox = null)
	{
		$this->attributes = $attributes;
		$this->cloudinbox = $cloudinbox;

		$this->fill();
	}


	/**
	 * Fill the resource with the array of attributes.
	 *
	 * @return void
	 *
	 */
	private function fill()
	{
		foreach ($this->attributes as $key => $value) {
			$key = $this->camelCase($key);
			$this->{$key} = $value;
		}

		foreach ($this->collections as $key => $cls) {
			if ( ! is_array($this->{$key})) {
				continue;
			}
			$this->cloudinbox->transformCollection($this->{$key}, $cls);
		}
	}


	/**
	 * Convert the key name to camel case.
	 *
	 * @param $key
	 *
	 */
	private function camelCase($key)
	{
		$parts = explode('_', $key);

		foreach ($parts as $i => $part) {
			if ($i !== 0) {
				$parts[$i] = ucfirst($part);
			}
		}

		return str_replace(' ', '', implode(' ', $parts));
	}


}
