<?php
// This file is for storing the secret key used for making jwts "securely".
//   A more secure storage mechanism should be used, but in the interim, this will make the application run.
//   For each instance that this application is deployed, a new secret key should be randomly generated and
//   pasted in to the secret_key field. To generate a secret key, use any random string of characters that is
//   at least 32 characters long (256 bits).

  if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	  die('Direct access not allowed');
	  exit();
  };

  // CHANGE TO NEW VALUE EVERY NEW DEPLOYMENT
  $secret_key = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  
?>