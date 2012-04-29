<?php

namespace Meinhof\Configuration;


interface ConfigurationInterface
{
    public function getPosts();

    public function getGlobals();

    public function getLayoutForPost($post);
}