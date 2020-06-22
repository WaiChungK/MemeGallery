<?php

namespace App\Utility;

class Utility {
	
	// ----------------------------------------------------------------------------------
	
	// Find all hashtags in the provided string
	// Return an array of hashtag without the '#' symbol
	
	// ==============================================================
	// Eg
	// ==============================================================
	// > Input:
	// str	: "#hello #world!!!, ### my name is #Mc#Donalds"
	// ==============================================================
	// > Expected output: ['hello', 'world', 'Mc', 'Donalds']
	// ==============================================================
	
	public static function FindHashtags($str)
	{
		
		// Decompose words with multiple # into array
		$func_flt_ht = function($str){ return explode('#', $str); };
		
		// Remove words without #
		$func_rm_no_ht = function($str){ return strpos($str, '#') !== false; };
		
		// Remove punctuation (Keep only letters and alphabets)
		$func_rm_punc = function($str){ return preg_replace('/[^a-z]+/i', '', $str); };
		
		// Remove invalid hashtags format
		$func_rm_inv_fm	= function($str){
			return strlen($str) > 0 && 
				$str[0] !== ' ' ; // Keeps those that fulfill these requirements
		};

		// Flatten an 2D array into 1D array\
		
		// Code was retrieved from
		// https://stackoverflow.com/questions/1319903/how-to-flatten-a-multidimensional-array
		// Retrieved at 27 FEB 2020

		$flatten_array = function (array $array) 
		{
			$return = array();
			array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
			return $return;
		};

		$a = explode(' ', $str);
		$a = array_filter( $a, $func_rm_no_ht );
		$a = array_map($func_flt_ht, $a);
		$a = $flatten_array($a);
		$a = array_map($func_rm_punc, $a);
		$a = array_filter( $a, $func_rm_inv_fm );
		
		return $a;
	}
	
	// ----------------------------------------------------------------------------------
	
	// Get the cosine similarity of two hashtags in each post's description
	
	public static function HashtagsDistance($ht1, $ht2, $caseSentitivity = false)
	{
		$a = $b = null;
		Utility::FlattenWordOccurenceDictionary(
			Utility::GetWordOccurence($ht1, $ht2, $caseSentitivity), $a, $b
		);
		return Utility::CosineSimilarity($a, $b);
	}

	// ----------------------------------------------------------------------------------
	
	// Get the cosine similarity of two integer arrays of identical length

	public static function CosineSimilarity($a, $b)
	{
		// Validation

		// Validate two arrays are the same length
		if ( count($a) !== count($b) ) return 0;
		
		$a_dot_b = 0;
		for( $i=0; $i<count($a); $i++) $a_dot_b += $a[$i] * $b[$i];

		$magnitude = function($a) {
			$f = function($acc, $cur){ return $acc + $cur*$cur; };
			return sqrt(array_reduce($a, $f, 0));
		};

		if (
			($a_mag = $magnitude($a)) == 0 ||
			($b_mag = $magnitude($b)) == 0 ) // Fixed division by zero	
			return 0;

		return $a_dot_b / ( $a_mag * $b_mag );

	}
	
	// ----------------------------------------------------------------------------------
	
	// Given TWO string array of any length, 
	// generate an integer array consists of the number of occurence 
	// of each word in each string array

	// ==============================================================
	// Eg
	// ==============================================================
	// > Input:
	// strAry1	: ['hello', 'my', 'name', 'is', 'Jeff', 'Jeff'];
	// strAry2	: ['hello', 'I', 'like', 'dog'];
	// ==============================================================
	// > Expected Output:
	// [
	//	'hello' => [1,1], 
	//	'my'	=> [1,0], 
	//	'name'	=> [1,0], 
	//	'is'	=> [1,0], 
	//	'Jeff'	=> [2,0],
	//	'I'		=> [0,1],
	//	'like'	=> [0,1],
	//	'dog'	=> [0,1],
	// ];
	// ==============================================================

	public static function GetWordOccurence($strAry1, $strAry2, $caseSentitivity = false)
	{
		$dictionary = [];

		// Iterate strAry1
		
		foreach($strAry1 as $str)
		{
			$str = $caseSentitivity ? $str : strtolower($str);
			if ( !array_key_exists($str, $dictionary) )
				$dictionary[ $str ] = [1, 0];
			else 
				$dictionary[ $str ][0] += 1;
		}

		// Iterate strAry2

		foreach($strAry2 as $str)
		{
			$str = $caseSentitivity ? $str : strtolower($str);
			if ( !array_key_exists($str, $dictionary) )
				$dictionary[ $str ] = [0, 1];
			else 
				$dictionary[ $str ][1] += 1;
		}

		return $dictionary;
	}

	// ----------------------------------------------------------------------------------
	
	// Flatten the 'GetWordOccurence' into two integer arrays

	public static function FlattenWordOccurenceDictionary($dict, &$a, &$b)
	{
		$a = [];
		$b = [];

		foreach($dict as $key => $value)
		{
			array_push($a, $value[0]);
			array_push($b, $value[1]);
		}
	}

	// ----------------------------------------------------------------------------------
	
}
