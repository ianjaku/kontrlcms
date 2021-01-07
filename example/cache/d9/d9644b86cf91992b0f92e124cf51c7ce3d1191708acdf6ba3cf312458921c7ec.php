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

/* about.twig */
class __TwigTemplate_2032d04545d3c216af80627efbcc7f6f13c0b3d089e6afa18e3491990d3bc7d1 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'hero' => [$this, 'block_hero'],
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "layouts/layout.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("layouts/layout.twig", "about.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_hero($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "
    <h1 class=\"hero__title\">
        ";
        // line 6
        echo call_user_func_array($this->env->getFunction('text')->getCallable(), [$context, [0 => "hero__title", 1 => "Your mind is a powerful thing. When you fill it with positive thoughts your life will begin to change"]]);
        echo "
        ";
        // line 8
        echo "    </h1>
    <p class=\"hero__text\">
        So, choose your thoughts, focus and take action!
    </p>

";
    }

    // line 16
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 17
        echo "
    <main class=\"main main--high container\">

        <div class=\"post\">
            <div class=\"post__content\">
                <a href=\"/\" class=\"post__home\">
                    Home
                    <svg width=\"8\" height=\"13\" viewBox=\"0 0 8 13\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">
                        <path d=\"M1 1L6.5 6.5L1 12\" stroke=\"black\"/>
                    </svg>
                </a>
                <div class=\"post__auto\">
                    ";
        // line 29
        echo call_user_func_array($this->env->getFunction('wysiwyg')->getCallable(), [$context, [0 => "page_text"]]);
        echo "
                </div>
            </div>
        </div>

    </main>

";
    }

    public function getTemplateName()
    {
        return "about.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  86 => 29,  72 => 17,  68 => 16,  59 => 8,  55 => 6,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "about.twig", "D:\\invacto\\projects\\cms\\withfatfree\\example\\views\\about.twig");
    }
}
