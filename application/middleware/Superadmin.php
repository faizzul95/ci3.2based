<?php

# application/middleware/Superadmin.php

class Superadmin implements Luthier\MiddlewareInterface
{
	public function run($args)
	{
		if (!isLoginCheck())
			return response(['code' => 401, 'message' => 'Login required!'], HTTP_UNAUTHORIZED);
		else if (currentUserRoleID() != 1)
			return response(['code' => 403, 'message' => 'Unauthorized: Access is denied'], HTTP_FORBIDDEN);
	}
}
