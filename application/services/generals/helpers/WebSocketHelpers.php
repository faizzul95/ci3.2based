<?php

namespace App\services\generals\helpers;

class WebSocketHelpers
{
	public function init()
	{
		// // Load package path
		library('WebSocket/Websocket');

		// // Run server
		ci()->websocket->set_callback('auth', array($this, '_auth'));
		ci()->websocket->set_callback('event', array($this, '_event'));
		ci()->websocket->run();
	}

	public function _auth($datas = null)
	{
		// Here you can verify everything you want to perform user login.
		// return hasData($datas, 'user_id', true, false);
		return (!empty($datas->user_id)) ? $datas->user_id : false;
	}

	public function _event($datas = null)
	{
		// Here you can do everyting you want, each time message is received
		echo 'Hey ! I\'m an EVENT callback' . PHP_EOL;
	}
}
