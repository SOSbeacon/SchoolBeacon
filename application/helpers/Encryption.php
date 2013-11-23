<?php

class Sos_Helper_Encryption {
	public static function encode($rawDigest, $bitsPerCharacter, $chars = NULL)
	{
	    if ($chars === NULL || strlen($chars) < 2) {
	        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_;';
	    }
	
	    if ($bitsPerCharacter < 1) {
	        // $bitsPerCharacter must be at least 1
	        $bitsPerCharacter = 1;
	
	    } elseif (strlen($chars) < pow(2, $bitsPerCharacter)) {
	        // Character length of $chars is too small for $bitsPerCharacter
	        // Set $bitsPerCharacter to greatest value allowed by length of $chars
	        $bitsPerCharacter = 1;
	
	        do {
	            $bitsPerCharacter++;
	        } while (strlen($chars) > pow(2, $bitsPerCharacter));
	    }
	
	    $bytes = unpack('C*', $rawDigest);
	    $byteCount = count($bytes);
	
	    $out = '';
	    $byte = array_shift($bytes);
	    $bitsRead = 0;
	
	    for ($i = 0; $i < $byteCount * 8 / $bitsPerCharacter; $i++) {
	
	        if ($bitsRead + $bitsPerCharacter > 8) {
	            // Not enough bits remain in this byte for the current character
	            // Get remaining bits and get next byte
	            $oldBits = $byte - ($byte >> 8 - $bitsRead << 8 - $bitsRead);
	
	            if (count($bytes) == 0) {
	                // Last bits; match final character and exit loop
	                $out .= $chars[$oldBits];
	                break;
	            }
	
	            $oldBitCount = 8 - $bitsRead;
	            $byte = array_shift($bytes);
	            $bitsRead = 0;
	
	        } else {
	            $oldBitCount = 0;
	        }
	
	        // Read only the needed bits from this byte
	        $bits = $byte >> 8 - ($bitsRead + ($bitsPerCharacter - $oldBitCount));
	        $bits = $bits - ($bits >> $bitsPerCharacter - $oldBitCount << $bitsPerCharacter - $oldBitCount);
	        $bitsRead += $bitsPerCharacter - $oldBitCount;
	
	        if ($oldBitCount > 0) {
	            // Bits come from seperate bytes, add $oldBits to $bits
	            $bits = ($oldBits << $bitsPerCharacter - $oldBitCount) | $bits;
	        }
	
	        $out .= $chars[$bits];
	    }
	
	    return $out;
	}
}		
	

?>