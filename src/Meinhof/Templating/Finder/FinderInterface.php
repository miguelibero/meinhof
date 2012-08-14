<?php

namespace Meinhof\Templating\Finder;

/**
 * A finder accepts a template name pattern ans should return an 
 * array of template names that match the pattern.
 */
interface FinderInterface
{
	/**
     * Finds the best template for a given pattern.
     *
	 * @return string existing template
	 */
	public function find($pattern);
}