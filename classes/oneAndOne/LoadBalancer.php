<?php

namespace oneAndOne;

/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 13/03/2016
 * Time: 13:52
 */
class LoadBalancer extends Element
{
    protected $segment = "/load_balancers";

	public function checkLoad($id)
	{
		$result = parent::get($id);

		if (isset($result->content->server_ips) && is_array($result->content->server_ips))
		{
			$serverclass = new \oneAndOne\Server;

			// Loop all servers in the balancer
			foreach ($result->content->server_ips as $server)
			{
				// Check if we have to optimize the server
				$newserver = $serverclass->optimize($server->id);

				// If we have a server object here, the optimize method cloned the server, so let's add it to the balancer
				if ($newserver instanceof \oneAndOne\Server)
				{
					$this->addServer($newserver);
				}
			}
		}
	}

	public function addServer(\oneAndOne\Server $server)
	{
		$servers = new \oneAndOne\LoadBalancer\ServerIPs;

		$result = $servers->post(array('server_ips' => $server->id));
    }
}
