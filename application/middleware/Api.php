<?php

# application/middleware/Api.php

class Api implements Luthier\MiddlewareInterface
{
	public function run($args)
	{
		if (!isAjax())
			return response(['code' => 422, 'message' => 'API is only accessible via AJAX REQUEST!'], HTTP_UNPROCESSABLE_ENTITY);
	}
}
