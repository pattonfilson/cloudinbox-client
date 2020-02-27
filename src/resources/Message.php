<?php

namespace cloudsystems\cloudinbox\resources;


class Message extends Resource
{

	public $id;
	public $status;
	public $messageId;
	public $from;
	public $to;
	public $subject;
	public $bodyText;
	public $bodyHtml;
	public $mailTime;
	public $createdAt;
	public $updatedAt;
	public $attachments;

	public $collections = [
		'attachments' => Attachment::class
	];

}
