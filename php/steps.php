<?php
	
	/* Разобваем бинарное открытое сообщение на 512-битные блоки */
	
	function break_binnary_open_message_into_512_bits_blocks($binnary_open_message){
		if(strlen($binnary_open_message) < 512)
			$array_of_512_bits_blocks[0] = $binnary_open_message;
		else{
			$j = 0;
			$step = 0;
			while(1){
				for($i = $step; $i < ($step + 512); $i++){
					$block_512 .= $binnary_open_message[$i];
				}
				$array_of_512_bits_blocks[$j] = $block_512;
				$block_512 = "";
				$j++;
				$step += 512;
				if(strlen($binnary_open_message) < ($step + 512)){
					$array_of_512_bits_blocks[$j] = substr($binnary_open_message, $step);
					break;
				}
			}
		}
		return $array_of_512_bits_blocks;
	}
	
	// расширяем последний блок до длины 448 по модулю 512
	function extend_array_of_512_bits_blocks($array_of_512_bits_blocks){
		$last_block = $array_of_512_bits_blocks[count($array_of_512_bits_blocks) - 1];

		if(strlen($last_block) === 512){
			$new_block = "1";
			while(strlen($new_block) < 448){
				$new_block .= "0";
			}
		}
		else{
			if(strlen($last_block) < 448){
				$last_block .= "1";
				while(strlen($last_block) < 448){
					$last_block .= "0";
				}
			}
			if((strlen($last_block) > 448) && (strlen($last_block) < 512)){
				$last_block .= "1";
				while(strlen($last_block) < 512){
					$last_block .= "0";
				}
				$new_block = "1";
				while(strlen($new_block) < 448){
					$new_block .= "0";
				}
			}
		}
		
		$array_of_512_bits_blocks[count($array_of_512_bits_blocks) - 1] = $last_block;
		if(isset($new_block))
			$array_of_512_bits_blocks[count($array_of_512_bits_blocks)] = $new_block;
		return $array_of_512_bits_blocks;
	}
	
	//  Добавляем длину сообщения (сразу преобразуем в big-endian формат)
	
	function extended_binnary_record_of_open_message_length($binnary_record_of_open_message_length){
		if(strlen($binnary_record_of_open_message_length) < 64){
			while(1){
				if(strlen($binnary_record_of_open_message_length) == 64)
					break;
				$binnary_record_of_open_message_length = "0".$binnary_record_of_open_message_length;	
			}
		}
		return $binnary_record_of_open_message_length;
	}
	
	// инициализируем 5 32-битных переменных
	function initialization_of_SHA_1_variables(){
		$a = extend_binnary_record_for_32_bits(base_convert("67452301", 16, 2)); 
		$b = extend_binnary_record_for_32_bits(base_convert("efcdab89", 16, 2));
		$c = extend_binnary_record_for_32_bits(base_convert("98badcfe", 16, 2));
		$d = extend_binnary_record_for_32_bits(base_convert("10325476", 16, 2));
		$e = extend_binnary_record_for_32_bits(base_convert("c3d2e1f0", 16, 2));
		return $SHA_1_variables = Array("a" => $a, "b" => $b, "c" => $c, "d" => $d, "e" => $e);
	}
	
	/* определим 4 нелинейные операции и 4 константы */
	
	function F_1($m, $l, $k){ // F(m, l, k) = (m AND l) OR (NOT m AND k), для 0 <= t <= 19  
		$not_m = bitwise_not($m);
		$f = (($m & $l) | ($not_m & $k));
		return $f;
	}
	
	function F_2($m, $l, $k){ // F(m, l, k) = m xor l xor k, для 20 <= t <= 39  
		$f = $m ^ $l ^ $k;
		return $f;
	}
	
	function F_3($m, $l, $k){ // F(m, l, k) = (m AND l) OR (m AND k) OR (l AND k), для 40 <= t <= 59 
		$f = (($m & $l) | ($m & $k) | ($l & $k));
		return $f;
	}
	
	function F_4($m, $l, $k){ // F(m, l, k) = m xor l xor k, для 60 <= t <= 79 
		$f = $m ^ $l ^ $k;
		return $f;
	}
	
	function initialization_of_SHA_1_constants(){ // константы
		$K_1 = extend_binnary_record_for_32_bits(base_convert("5a827999", 16, 2)); // для 0 <= t <= 19  
		$K_2 = extend_binnary_record_for_32_bits(base_convert("6ed9eba1", 16, 2)); // для 20 <= t <= 39 
		$K_3 = extend_binnary_record_for_32_bits(base_convert("8f1bbcdc", 16, 2)); // для 40 <= t <= 59 
		$K_4 = extend_binnary_record_for_32_bits(base_convert("ca62c1d6", 16, 2)); // для 60 <= t <= 79 
		return $SHA_1_constants = Array("K_1" => $K_1, "K_2" => $K_2, "K_3" => $K_3, "K_4" => $K_4);
	}
	
	/* (основной цикл) */
	
	function main_cycle($array_of_512_bits_blocks){ //  
		$SHA_1_variables = initialization_of_SHA_1_variables(); // вытаскиваем наш инициализирующий вектор
		
		$a = $SHA_1_variables["a"];
		$b = $SHA_1_variables["b"];
		$c = $SHA_1_variables["c"];
		$d = $SHA_1_variables["d"];
		$e = $SHA_1_variables["e"];
		
		$SHA_1_constants = initialization_of_SHA_1_constants(); // вытаскиваем константы
		$K_1 = $SHA_1_constants["K_1"];
		$K_2 = $SHA_1_constants["K_2"];
		$K_3 = $SHA_1_constants["K_3"];
		$K_4 = $SHA_1_constants["K_4"];
		
		$h0 = $a;
		$h1 = $b;
		$h2 = $c;
		$h3 = $d;
		$h4 = $e;
		
		// сначала цикл по 512-битным блокам
		for($i = 0; $i < count($array_of_512_bits_blocks); $i++){
			$block_of_512_bits = $array_of_512_bits_blocks[$i];
			$M = break_into_32_bits_words($block_of_512_bits);
			
			// цитирую (википедия): блок сообщения преобразуется из 16 32-битовых слов Mi в 80 32-битовых слов Wj
			$W = $M;
			$W = extending_W($W); 

			for($t = 0; $t < 80; $t++){
				if(($t >= 0) && ($t <= 19)){
					$F = F_1($b, $c, $d);
					$K = $K_1;
				}
				if(($t >= 20) && ($t <= 39)){
					$F = F_2($b, $c, $d);
					$K = $K_2;
				}
				if(($t >= 40) && ($t <= 59)){
					$F = F_3($b, $c, $d);
					$K = $K_3;
				}
				if(($t >= 60) && ($t <= 79)){
					$F = F_4($b, $c, $d);
					$K = $K_4;
				}
				$temp = bindec(cyclic_shift_to_the_left($a, 5)) + bindec($F) + bindec($e) + bindec($W[$t]) + bindec($K);
				//$temp = extend_binnary_record_for_32_bits(decbin($temp))."<br /><br />";
				
				$e = $d;
				$d = $c;
				$c = cyclic_shift_to_the_left($b, 30);
				$b = $a;
				$a = decbin($temp);
				
				$h0 = extend_binnary_record_for_32_bits(decbin(bindec($h0) + bindec($a)));
				$h1 = extend_binnary_record_for_32_bits(decbin(bindec($h1) + bindec($b)));
				$h2 = extend_binnary_record_for_32_bits(decbin(bindec($h2) + bindec($c)));
				$h3 = extend_binnary_record_for_32_bits(decbin(bindec($h3) + bindec($d)));
				$h4 = extend_binnary_record_for_32_bits(decbin(bindec($h4) + bindec($e)));
			}
		}
		$SHA_1_digest = base_convert($h0, 2, 16).base_convert($h1, 2, 16).base_convert($h2, 2, 16).base_convert($h3, 2, 16).base_convert($h4, 2, 16);
		echo var_dump($SHA_1_digest);
	}
	
	function extending_W($W){
		while(count($W) < 80){
			$W[] = cyclic_shift_to_the_left(extend_binnary_record_for_32_bits(decbin(bindec($W[count($W) - 3]) ^ bindec($W[count($W) - 8]) ^ bindec($W[count($W) - 14]) ^ bindec($W[count($W) - 16]))), 1);
		}	
		return $W;
	}
?>