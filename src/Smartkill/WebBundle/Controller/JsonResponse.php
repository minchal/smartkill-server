<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class JsonResponse extends Response {
	
	public function __construct($content = '', $status = 200, $headers = array()) {
		$headers['Content-Type'] = 'application/json';
		parent::__construct(json_encode($content), $status, $headers);
	}
	
}
