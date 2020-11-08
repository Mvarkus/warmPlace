<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* 404.html.twig */
class __TwigTemplate_d766047efebde2711517e291bd845a210782fe5b6cd4493b262ff3a5f1a95c69 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'header' => [$this, 'block_header'],
            'pageTitle' => [$this, 'block_pageTitle'],
            'main' => [$this, 'block_main'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("base.html.twig", "404.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo "Not Found";
    }

    // line 4
    public function block_header($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 5
        echo "  ";
        $this->displayParentBlock("header", $context, $blocks);
        echo "
";
    }

    // line 8
    public function block_pageTitle($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo "Not found";
    }

    // line 10
    public function block_main($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 11
        echo "<div class=\"block-container\">
    <div class=\"empty-result\">404 Error | Page you are looking for was not found</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "404.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 11,  74 => 10,  67 => 8,  60 => 5,  56 => 4,  49 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "404.html.twig", "/var/www/nhl.v/views/404.html.twig");
    }
}
