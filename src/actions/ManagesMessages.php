<?php

namespace cloudsystems\cloudinbox\actions;

use cloudsystems\cloudinbox\resources\Message;
use cloudsystems\cloudinbox\exceptions\ValidationException;


trait ManagesMessages
{


	/**
	 * List all messages
	 *
	 * @param  array $params Optional query parameters
	 * @return Message[]
	 *
	 */
	public function getMessages(array $params = [])
	{
		$query = [];
		$query = array_merge($query, $params);
		$query = array_filter($query, 'strlen');
		$data = $this->getAllItems("messages", $query);
		return $this->transformCollection($data, Message::class);
	}


	public function updateMessageStatus(int $id, string $status)
	{
		$result = $this->post("messages/status/{$id}/{$status}");
		return new Message($result, $this);
	}


	public function markAsRead(int $id)
	{
		return $this->updateMessageStatus($id, 'read');
	}


}
