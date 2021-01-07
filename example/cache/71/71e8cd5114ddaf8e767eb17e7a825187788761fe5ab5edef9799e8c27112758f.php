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

/* home.twig */
class __TwigTemplate_99de6279adff7e83f08f6e7683903c612804376204e2833e733cfd910a8ceef1 extends Template
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
        $this->parent = $this->loadTemplate("layouts/layout.twig", "home.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_hero($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 5
        echo "
    <h1 class=\"hero__title\">
        ";
        // line 7
        echo call_user_func_array($this->env->getFunction('text')->getCallable(), [$context, [0 => "hero__title", 1 => "Your mind is a powerful thing. When you fill it with positive thoughts your life will begin to change"]]);
        echo "
";
        // line 9
        echo "    </h1>
    <p class=\"hero__text\">
        So, choose your thoughts, focus and take action!
    </p>

";
    }

    // line 17
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 18
        echo "
<main class=\"main container\">
";
        // line 22
        echo "
    <div class=\"double-info-block\">

        <section class=\"info-block\">
            <!-- <div class=\"info-block__image\" style=\"background-image: url('img/mission.jpg')\"></div> -->
            <!-- <div class=\"info-block__overlay\"></div> -->
            <div class=\"info-block__bg-color\"></div>
            <div class=\"info-block__center-text\">
                <h2 class=\"info-block__center-text__title\">
                    ";
        // line 31
        echo call_user_func_array($this->env->getFunction('text')->getCallable(), [$context, [0 => "center-text__title", 1 => "Do what you love and love what you do"]]);
        echo "
                </h2>
                <a href=\"\" class=\"button button--accent2 info-block__center-text__button\">
                    ";
        // line 34
        echo call_user_func_array($this->env->getFunction('text')->getCallable(), [$context, [0 => "center-text__button", 1 => "Loopbaancoaching"]]);
        echo "
                </a>
            </div>
        </section>

        <section class=\"info-block\">
            <!-- <div class=\"info-block__image\" style=\"background-image: url('img/mission.jpg')\"></div> -->
            <!-- <div class=\"info-block__overlay\"></div> -->
            <div class=\"info-block__bg-color\"></div>
            <div class=\"info-block__center-text\">
                <h2 class=\"info-block__center-text__title\">
                    ";
        // line 45
        echo call_user_func_array($this->env->getFunction('text')->getCallable(), [$context, [0 => "center-text__title", 1 => "Focus on perfectionism"]]);
        echo "
                </h2>
                <a href=\"\" class=\"button button--accent2 info-block__center-text__button\">
                    ";
        // line 48
        echo call_user_func_array($this->env->getFunction('text')->getCallable(), [$context, [0 => "center-text__button", 1 => "Perfectionisme"]]);
        echo "
                </a>
            </div>
        </section>

    </div>

";
        // line 56
        echo "    ";
        echo call_user_func_array($this->env->getFunction('img')->getCallable(), [$context, [0 => "testImage", 1 => "/img/orange.jpg", 2 => ""]]);
        echo "

    <section class=\"info-block info-block--free\">
";
        // line 60
        echo "        <div class=\"info-block__image\" ";
        echo call_user_func_array($this->env->getFunction('bgImg')->getCallable(), [$context, [0 => "ending_image", 1 => "/img/orange.jpg"]]);
        echo " ></div>

        <!-- <div class=\"info-block__overlay\"></div> -->
        <div class=\"info-block__ending\">
            <h2 class=\"info-block__ending__title\">
                ";
        // line 65
        echo call_user_func_array($this->env->getFunction('text')->getCallable(), [$context, [0 => "ending__title", 1 => "Wil jij ook ontdekken hoe sterk perfectionisme jouw leven beheerst?"]]);
        echo "
            </h2>
            <p class=\"info-block__ending__text\">
                ";
        // line 68
        echo call_user_func_array($this->env->getFunction('text')->getCallable(), [$context, [0 => "ending__text", 1 => "
                Vraag dan vrijblijvend onze vragenlijst aan.
                Tijdens een kennismakingsgesprek bekijken we samen
                je noden en behoeften en schatten we in hoeveel sessies er nodig zijn.
                Schrijf je in voor een gratis telefonisch kennismakingsgesprek:
                        "]]);
        // line 78
        echo "
            </p>
            <a href=\"/about\" class=\"button button--accent5-text info-block__ending__button\">
                ";
        // line 81
        echo call_user_func_array($this->env->getFunction('text')->getCallable(), [$context, [0 => "ending__button", 1 => "Vragenlijst en telefonisch kennismakingsgesprek"]]);
        echo "
            </a>
            <div class=\"info-text info-block__ending__info\">
                <div class=\"info-text__icon\">
                    <img src=\"/img/info.svg\" alt=\"\">
                </div>
                <div class=\"info-text__text\">
                    ";
        // line 88
        echo call_user_func_array($this->env->getFunction('text')->getCallable(), [$context, [0 => "ending__info", 1 => "
                    Een gemiddeld traject duurt ongeveer 6 sessies van 1 tot 2 uur.
                    Ga je als particulier aan de slag met je perfectionisme patroon,
                    dan rekenen we 60 euro per uur. Ons zakelijk tarief is 90 euro excl. btw per uur."]]);
        // line 96
        echo "
                </div>
            </div>
        </div>
    </section>
</main>

";
    }

    public function getTemplateName()
    {
        return "home.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  172 => 96,  167 => 88,  157 => 81,  152 => 78,  145 => 68,  139 => 65,  130 => 60,  123 => 56,  113 => 48,  107 => 45,  93 => 34,  87 => 31,  76 => 22,  72 => 18,  68 => 17,  59 => 9,  55 => 7,  51 => 5,  47 => 4,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "home.twig", "D:\\invacto\\projects\\cms\\withfatfree\\example\\views\\home.twig");
    }
}
