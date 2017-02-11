<?php
class IC {
	/*
	Verification of IC
	@param: string $ic, given IC
	@return: boolean, 0-false | 1-true
	*/

	function verifyIC($ic)
	{
	   $ic = preg_replace('#\s+#', '', $ic);
	   if (!preg_match('#^\d{8}$#', $ic)) {
      	return FALSE;
	   }
	  	// kontrolní součet
    	$a = 0;
    	for ($i = 0; $i < 7; $i++) {
        $a += $ic[$i] * (8 - $i);
	   }
    	$a = $a % 11;
    	if ($a === 0) {
	       $c = 1;
	    } elseif ($a === 1) {
	       $c = 0;
    	 } else {
        	 $c = 11 - $a;
    	 }
	 	 //vraci true nebo false
    	 return (int) $ic[7] === $c;
	 }
}
?>
