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

/* base.html.twig */
class __TwigTemplate_57d373dc3ed68afcc1653a31ed240f1bc557ecb11adf23ede57dde208374d458 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'head' => [$this, 'block_head'],
            'title' => [$this, 'block_title'],
            'metaDescription' => [$this, 'block_metaDescription'],
            'scripts' => [$this, 'block_scripts'],
            'rentMenuLink' => [$this, 'block_rentMenuLink'],
            'aboutMenuLink' => [$this, 'block_aboutMenuLink'],
            'contactMenuLink' => [$this, 'block_contactMenuLink'],
            'pageTitle' => [$this, 'block_pageTitle'],
            'main' => [$this, 'block_main'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
  ";
        // line 4
        $this->displayBlock('head', $context, $blocks);
        // line 17
        echo "</head>
<body>
<header>
    <div class=\"get-in-touch\">
        <div class=\"contact-details\">
            <div class=\"detail\">
                <img draggable=\"false\" src=\"/images/icons/ph2.png\" alt=\"phone icon\">
                <span>01604 249 481</span>
            </div>

            <div class=\"detail\">
                <img draggable=\"false\" src=\"/images/icons/e4.png\" alt=\"phone icon\">
                <span>wp@bla.gmail</span>
            </div>
        </div>
    </div>
    <div class=\"logo\">
        <span class=\"logo-title\">WarmPlace</span>
    </div>
    <nav>
      <ul>
        <li><a draggable=\"false\" class=\"menu-button ";
        // line 38
        $this->displayBlock('rentMenuLink', $context, $blocks);
        echo "\" href=\"/\">Properties</a></li>
        <li><a draggable=\"false\" class=\"menu-button ";
        // line 39
        $this->displayBlock('aboutMenuLink', $context, $blocks);
        echo "\" href=\"/about\">About us</a></li>
        <li><a draggable=\"false\" class=\"menu-button ";
        // line 40
        $this->displayBlock('contactMenuLink', $context, $blocks);
        echo "\" href=\"/contact\">Find us</a></li>
      </ul>
    </nav>
</header>
<h1 class=\"middle-title\"><span>";
        // line 44
        $this->displayBlock('pageTitle', $context, $blocks);
        echo "</span></h1>
<main>
";
        // line 46
        $this->displayBlock('main', $context, $blocks);
        // line 47
        echo "</main>
<footer>
  
  <div class=\"icons\">
    <div class=\"icon\">
      <a draggable=\"false\" href=\"https://facebook.com\" title=\"Our facebook page\">
        <img draggable=\"false\" src=\"/images/icons/fb22.png\" alt=\"facebook icon\">
      </a>
    </div>
    <div class=\"icon\">
      <a draggable=\"false\" href=\"https://twitter.com\" title=\"Our twitter page\">
        <img draggable=\"false\" src=\"/images/icons/tw2.png\" alt=\"twitter icon\">
      </a>
    </div>
  </div>
  <div class=\"copyright\">
    <span>&#169; 2019 example.com</span>
  </div>
</footer>
</body>
</html>
";
    }

    // line 4
    public function block_head($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 5
        echo "    <title>";
        $this->displayBlock('title', $context, $blocks);
        echo " - Warm | place</title>

    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">

    <meta name=\"description\" content=\"";
        // line 10
        $this->displayBlock('metaDescription', $context, $blocks);
        echo "\">

    <link rel=\"stylesheet\" href=\"/css/normalize.css\">
    <link rel=\"stylesheet\" href=\"/css/main.css\">

    ";
        // line 15
        $this->displayBlock('scripts', $context, $blocks);
        // line 16
        echo "  ";
    }

    // line 5
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 10
    public function block_metaDescription($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 15
    public function block_scripts($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 38
    public function block_rentMenuLink($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 39
    public function block_aboutMenuLink($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 40
    public function block_contactMenuLink($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 44
    public function block_pageTitle($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 46
    public function block_main($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    public function getTemplateName()
    {
        return "base.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  192 => 46,  186 => 44,  180 => 40,  174 => 39,  168 => 38,  162 => 15,  156 => 10,  150 => 5,  146 => 16,  144 => 15,  136 => 10,  127 => 5,  123 => 4,  98 => 47,  96 => 46,  91 => 44,  84 => 40,  80 => 39,  76 => 38,  53 => 17,  51 => 4,  46 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "base.html.twig", "/var/www/nhl.v/views/base.html.twig");
    }
}
