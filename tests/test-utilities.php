<?php

// use reflection in order to test protected and private methods
function get_method($class, $name) {
  $class = new ReflectionClass($class);
  $method = $class->getMethod($name);
  $method->setAccessible(true);
  return $method;
}
