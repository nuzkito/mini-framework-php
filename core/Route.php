<?php

class Route {

	protected $uri;
	protected $closure;
	// protected $uriPattern;

	const PARAMETER_PATTERN = '/:([^\/]+)/';
	const PARAMETER_REPLACEMENT = '(?<\1>[^/]+)';
	protected $parameters;

	public function __construct($uri, $closure)
	{
		$this->uri = $uri;
		$this->closure = $closure;
	}

	public function getUriPattern()
	{
		$uriPattern = preg_replace(self::PARAMETER_PATTERN, self::PARAMETER_REPLACEMENT, $this->uri);
		$uriPattern = str_replace('/', '\/', $uriPattern);
		$uriPattern = '/^' . $uriPattern . '\/*$/s';
		return $uriPattern;
	}

	public function getParameterNames()
	{
		preg_match_all(self::PARAMETER_PATTERN, $this->uri, $parameterNames);
		return array_flip($parameterNames[1]);
	}

	public function resolveParameters($matches)
	{
		$this->parameters = array_intersect_key($matches, $this->getParameterNames());
	}

	public function getParameters()
	{
		return $this->parameters;
	}

	public function checkIfMatch($requestUri)
	{
		$uriPattern = $this->getUriPattern();
		if (preg_match($uriPattern, $requestUri, $matches))
		{
			$this->resolveParameters($matches);
			return true;
		}
		return false;
	}

	public function execute()
	{
		$closure = $this->closure;
		$parameters = $this->getParameters();
		return call_user_func_array($closure, $parameters);
	}

}

