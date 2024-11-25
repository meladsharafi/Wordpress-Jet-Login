<?php

function get_user_ip()
{
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    return $_SERVER['HTTP_CLIENT_IP'];
  }

  if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    return $_SERVER['HTTP_X_FORWARDED_FOR'];
  }

  return $_SERVER['REMOTE_ADDR'];
}

function num_persian_to_english($string)
{
  $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
  $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

  $output = str_replace($persian, $english, $string);
  return $output;
}

function password_generate($chars)
{
  // Define a string containing all possible characters for the password
  $data = '@#&*^=-1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
  // Shuffle the characters in the string and extract a substring of length $chars
  return substr(str_shuffle($data), 0, $chars);
}

function get_url_query_value($url, $query_key)
{
  // Initialize URL to the variable
  // Use parse_url() function to parse the URL 
  // and return an associative array which
  // contains its various components
  $url_components = parse_url($url);
  // Use parse_str() function to parse the
  // string passed via URL
  parse_str($url_components['query'], $params);

  if ($params[$query_key]) {
    return $params[$query_key];
  }
  return '';
  // Display result
}
