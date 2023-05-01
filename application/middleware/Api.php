<?php

# application/middleware/Api.php

use App\services\general\traits\SecurityRateLimittingTrait;

class Api implements Luthier\MiddlewareInterface
{
	use SecurityRateLimittingTrait;
	public function run($args)
	{
		if (!isAjax()) {
			return response(['code' => 422, 'message' => 'API is only accessible via AJAX REQUEST!'], HTTP_UNPROCESSABLE_ENTITY);
		} else {
			$this->check_rate_limit();
		}
	}
}
