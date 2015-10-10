<?php

namespace Lyra\Interfaces;

interface Router
{
	public function addRoute($route);
	public function resolve($url);
}