<?php

namespace Meinhof\Export;

interface StoreInterface
{
    public function store($url, $content);
}
