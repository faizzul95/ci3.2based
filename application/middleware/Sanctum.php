<?php

# application/middleware/Sanctum.php

use App\middleware\core\traits\XssProtectionTrait;

class Sanctum implements Luthier\MiddlewareInterface
{
	public function run($args)
	{
		// Get the Authorization header
		$authorizationHeader = ci()->input->get_request_header('Authorization', TRUE);

		// Remove "Bearer " from the header value
		$token = !empty($authorizationHeader) ? str_replace('Bearer ', '', $authorizationHeader) : NULL;

		if (empty($token)) {
			if (!isLoginCheck()) {
				if (isAjax())
					return response(['code' => 401, 'message' => 'Login is required!'], HTTP_UNAUTHORIZED);
				else
					redirect('', true);
			}
		} else {
			$data = validate_jwt_token($token);
			if ($data === false)
				return response(['code' => 401, 'message' => 'Login is required!'], HTTP_UNAUTHORIZED);
		}
	}
}
