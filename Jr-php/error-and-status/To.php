<?php
namespace Jr;
class To {
	static function string($var, $pop=0) {
		if (is_scalar($var) || method_exists($var , '__toString')) return "$var";
		$msg = ' cannot be converted to string';
		$msg = ($t=gettype($var))==="object" ? get_class($var)." $t$msg" : $t.$msg;
		throw new Exc(array("To::string: $msg", 'pop'=>$pop+1));
	}
	// protected function 
}



function run2() {
	$arr = array();
	$arr = new stdClass();
	try { To::string($arr); }
	catch (Exception $e) {
		echo "$e";
		var_dump($e->toArray());
		// throw new Exc(['rethrowing...', $e, 'pop'=>1]);
	}
}
function run1() {
	run2();
}

run1();
