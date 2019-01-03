<?php
	require_once "auxiliary_functions.php"; // набор функций для конвертации из одной системы счисления в другую
	require_once "steps.php"; // набор функций для выполнения шагов алгоритма
	
	if($_POST["open_message"] !== false){
		$open_message = $_POST["open_message"];
		sha_1($open_message);
	}
	
	function sha_1($open_message){
		$binnary_open_message = "";
		for($i = 0; $i < strlen($open_message); $i++){
			$binnary_open_message .= bstr2bin($open_message[$i]); // представляем сообщение в бинарном виде
		}
		
		// шаг 1 (разбиваем бинарное открытое сообщение на 512-битные блоки)
		$array_of_512_bits_blocks = break_binnary_open_message_into_512_bits_blocks($binnary_open_message);
		
		// расширяем бинарное открытое сообщение до длины 448 по модулю 512
		$extended_array_of_512_bits_blocks = extend_array_of_512_bits_blocks($array_of_512_bits_blocks);
		
		// представляем длину исходного сообщения в двоичном виде
		$binnary_record_of_open_message_length = decbin(strlen($open_message)); 
		
		// представляем двоичною запись длины сообщения в 64-битном формате в формате big-endian
		$extended_binnary_record_of_open_message_length = extended_binnary_record_of_open_message_length($binnary_record_of_open_message_length); 
		
		// расширяем последний блок до 512 бит добавлением двоичной записи длины сообщения в 64-битном формате в формате big-endian
		$extended_array_of_512_bits_blocks[count($extended_array_of_512_bits_blocks) - 1] .= $extended_binnary_record_of_open_message_length;
		
		main_cycle($extended_array_of_512_bits_blocks);
	}
?>