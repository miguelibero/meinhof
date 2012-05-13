<?php

namespace Meinhof\Templating\Twig\Extension;

use Symfony\Component\Translation\TranslatorInterface;
use Meinhof\Templating\Twig\TokenParser\TransTokenParser;
use Meinhof\Templating\Twig\TokenParser\TransChoiceTokenParser;

class TranslationExtension extends \Twig_Extension
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'trans' => new \Twig_Filter_Method($this, 'trans'),
            'transchoice' => new \Twig_Filter_Method($this, 'transchoice'),
        );
    }

    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return array An array of Twig_TokenParser instances
     */
    public function getTokenParsers()
    {
        return array(
            // {% trans %}Symfony is great!{% endtrans %}
            new TransTokenParser(),

            // {% transchoice count %}
            //     {0} There is no apples|{1} There is one apple|]1,Inf] There is {{ count }} apples
            // {% endtranschoice %}
            new TransChoiceTokenParser(),
        );
    }

    public function trans($message, array $arguments = array(), $domain = "messages", $locale = null)
    {
        return $this->translator->trans($message, $arguments, $domain, $locale);
    }

    public function transchoice($message, $count, array $arguments = array(), $domain = "messages", $locale = null)
    {
        return $this->translator->transChoice($message, $count, array_merge(array('%count%' => $count), $arguments), $domain, $locale);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'translation';
    }

}