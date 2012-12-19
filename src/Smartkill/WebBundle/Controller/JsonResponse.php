<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class JsonResponse extends Response {
	
	public function __construct($content = '', $status = 200, $headers = array()) {
		$headers['Content-Type'] = 'application/json';
		
		if (is_array($content) || is_object($content)) {
			$content = json_encode($content);
		}
		
		parent::__construct($content, $status, $headers);
	}
	
}
