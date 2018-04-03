<?php

class ApplyFilterExtension extends Twig_Extension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'apply_filter_twig_extension';
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('apply_filter', array($this, 'applyFilter'), [
                    'needs_environment' => true,
                ]
            ));
    }

    public function applyFilter(Twig_Environment $env, $value, $filterName)
    {
        $twigFilter = $env->getFilter($filterName);

        if (!$twigFilter) {
            return $value;
        }

        return call_user_func($twigFilter->getCallable(), $value);
    }
}