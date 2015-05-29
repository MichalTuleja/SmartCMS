<?php

interface PictureInterface
{
    function save();
    function load();
}

interface CategoryInterface
{
    function load();
    function save();
    function remove();
}


interface CategoriesListInterface
{
    function fetch();
}


interface PicturesListInterface
{
    function fetch();
}

interface RandomPicturesInterface
{
    function fetch();
}


?>
