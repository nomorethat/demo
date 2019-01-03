<?php

	/* функции перевода данных между различными системами счисления */

	function char2hex($text){//из символа в хекс
		$value = unpack('H*', $text);
		return base_convert($value[1], 16, 16);
	}
	
	function hexbin($hex){ 
		$bin=''; 
		for($i=0;$i<strlen($hex);$i++) 
			$bin.=str_pad(decbin(hexdec($hex{$i})),4,'0',STR_PAD_LEFT); 
		return $bin; 
	} 
	
	function hex2bin($str) {
		$bin = "";
		$i = 0;
		do {
			$bin .= chr(hexdec($str{$i}.$str{($i + 1)}));
			$i += 2;
		} while ($i < strlen($str));
		return $bin;
	}
	
	function bin2bstr($input){ //из бинарных в текстовые
	// Convert a binary expression (e.g., "100111") into a binary-string
		if (!is_string($input)) return null; // Sanity check
		// Pack into a string
		return pack('H*', base_convert($input, 2, 16));
	}
	
	// Returns string(3) "ABC"
	//var_dump(bin2bstr('01000001 01000010 01000011'));
	// Returns string(24) "010000010100001001000011"
	//var_dump(bstr2bin('ABC'));
	
	function bstr2bin($input){//из текстовых в бинарные
		// Binary representation of a binary-string
		if (!is_string($input)) return null; // Sanity check
		// Unpack as a hexadecimal string
		$value = unpack('H*', $input);
		// Output binary representation
		return base_convert($value[1], 16, 2);
	}
	//echo bstr2bin("hello world");
	
	
	/* Вспомогательные функции, симулирующие битовые операции */
	
	function bitwise_not($param){ // связано с тем, что тильда не работает для битовой строки, а все преобразователи возвращают двоичное число в виде строки
		for($i = 0; $i < strlen($param); $i++){
			if($param[$i] === "0")
				$param[$i] = "1";
			else
				$param[$i] = "0";
		}
		return $param;
	}
	
	function cyclic_shift_to_the_left($str, $shift){ // циклический сдвиг влево
		$rez = substr($str, $shift).substr($str, 0, -(strlen($str) - $shift));
		return $rez;
	}
	
	function extend_binnary_record_for_32_bits($param){ // чтобы не было обрезки старших нулевых разрядов (дополнение до 32 бит)
		if(strlen($param) < 32){
			while(1){
				if(strlen($param) == 32)
					break;
				$param = "0".$param;	
			}
		}
		return $param;
	}
	
	/* вспомогательные функции */
	
	function break_into_32_bits_words($block_of_512_bits){ // разбиваем на слова по 32 бита
		$j = 0;
		$step = 0;
		while(1){
			if($step === strlen($block_of_512_bits))
				break;
			for($i = $step; $i < ($step + 32); $i++){
				$machine_word .= $block_of_512_bits[$i];
			}
			$array_of_32_bits_words[$j] = $machine_word;
			$machine_word = "";
			$j++;
			$step += 32;
		}
		return $array_of_32_bits_words;
	}
	
	function my_XOR($a, $b){
		$rez = "";
		for($c = 0; $c < strlen($a); $c++){
			if($a[$c] === $b[$c])
				$rez .= "0";
			else
				$rez .= "1";
		}
		echo $a."<br />".$b."<br />".$rez;
		echo "<br /><br />";
	}
?>