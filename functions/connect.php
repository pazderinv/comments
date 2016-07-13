<?php

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "commnets_test");
define("DB_PRE", "tree_");

$link = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)
	or die("Connect failed: ".mysqli_connect_error());

mysqli_query($link, "SET NAMES 'utf8'")
	or die("Encoding failed: ".mysqli_error($link));