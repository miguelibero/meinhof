<?php

namespace Meinhof\Configuration;


interface ConfigurationInterface
{
    public function getPosts();

    public function getTemplates();

    public function getGlobals();

    public function getLayoutForPost($post);

    public function getAsseticResourceForPost($post);

    public function savePost($post, $content);
}