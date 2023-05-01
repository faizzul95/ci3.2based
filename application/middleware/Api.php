<?php

# application/middleware/Api.php

use App\services\general\traits\SecurityRateLimitingTrait;

class Api implements Luthier\MiddlewareInterface
{
	use SecurityRateLimitingTrait;

	public function run($args)
	{
		if (!checkMaintenance()) {
			if (!isAjax())
				return response(['code' => 422, 'message' => 'API is only accessible via AJAX REQUEST!'], HTTP_UNPROCESSABLE_ENTITY);
			else
				$this->isRateLimited();
		} else {
			return response(['code' => 500, 'message' => 'System under maintenance'], HTTP_INTERNAL_ERROR);
		}
	}
}
