<?php

interface ArticleInterface
{
	function save();
	function load();
	function drop();
}


interface ArticlesListInterface
{
	function load();
}

?>
