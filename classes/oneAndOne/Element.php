<?php

namespace oneAndOne;
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 13/03/2016
 * Time: 13:53
 */
abstract class Element implements IElement
{
	protected $data;

    static $segment;

	public function __construct($data)
	{
		$this->data = $data;
	}

	/**
	 * @param null $id
	 * @return $this
	 * @throws \Exception
	 */
    public static function get($id = null)
    {
        $curl = new \transporter\Curl(\AppConfig::getData('API')['token']);
        $url= \AppConfig::getData('API')['url']. static::$segment.'/'.$id;
        $result = $curl->get($url);

		return static::createObject($result);
	}

	protected static function createObject($result)
	{
		// @TODO: check if $result is valid
		$content = $result->content;

		$return = null;

		if (is_array($content))
		{
			$return = array();

			foreach ($content as $data)
			{
				$return[] = new static($data);
			}
		}
		else
		{
			$return = new static($content);
		}

		return $return;
	}

	public function post($postparams)
	{
        $curl = new \transporter\Curl(\AppConfig::getData('API')['token']);
        $url= \AppConfig::getData('API')['url'].$this->segment;
        $result = $curl->post($url, $postparams);

		return $result;
	}

	public function __get($name)
	{
		if (isset($this->data->$name))
		{
			return $this->data->$name;
		}

		return null;
	}
}
