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

/* layouts/layout.twig */
class __TwigTemplate_e7b80d6f9e749f3aba75ee6e260c947cfa955aa0cd8bb9ea2b4d78ba91a12d30 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'hero' => [$this, 'block_hero'],
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <!-- <meta charset=\"UTF-8\"> -->
    <meta charset=\"<?php bloginfo( 'charset' ); ?>\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <link href=\"https://fonts.googleapis.com/css2?family=PT+Sans:wght@400;700&family=Spectral:wght@600;700&display=swap\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" href=\"/css/app.css\">
    <script src=\"/js/app.js\" defer></script>
    <link rel=\"shortcut icon\" href=\"/favicon.ico\" type=\"image/x-icon\">
    ";
        // line 11
        echo call_user_func_array($this->env->getFunction('head')->getCallable(), [$context]);
        echo "
</head>
<body>

    <div aria-hidden=\"true\" class=\"home__blob1\" style=\"background-image: url('/img/blob1.svg')\"></div>
    <div aria-hidden=\"true\" class=\"home__blob2\" style=\"background-image: url('/img/blob2.svg')\"></div>
    <div aria-hidden=\"true\" class=\"home__blob3\" style=\"background-image: url('/img/blob3.svg')\"></div>
    <div aria-hidden=\"true\" class=\"home__blob4\" style=\"background-image: url('/img/blob4.svg')\"></div>

    <nav class=\"mobile-nav\" id=\"mobile-nav\">
        <ul class=\"mobile-nav__list\">
            <li>
                <a class=\"mobile-nav__link\" href=\"\">Home</a>
            </li>
            <li>
                <a class=\"mobile-nav__link\" href=\"\">Wat doen we</a>
            </li>
            <li>
                <a class=\"mobile-nav__link\" href=\"\">Loopbaan coaching</a>
            </li>
            <li>
                <a class=\"mobile-nav__link\" href=\"\">Perfectionisme coaching</a>
            </li>
            <li>
                <a class=\"mobile-nav__link\" href=\"\">Hypnose</a>
            </li>
            <li>
                <a class=\"mobile-nav__link\" href=\"\">Wij we zijn</a>
            </li>
            <li>
                <a class=\"mobile-nav__link\" href=\"\">Waar we voor staan</a>
            </li>
        </ul>
    </nav>

    <header class=\"main-menu container\">
        <div class=\"logo\">
            <img src=\"/img/logo.png\" alt=\"IJK Coaching logo\">
        </div>
        <div class=\"hamburger main-menu__hamburger\" id=\"mobile-nav__open\">
            <div class=\"hamburger__slice\"></div>
            <div class=\"hamburger__slice\"></div>
            <div class=\"hamburger__slice\"></div>
        </div>
        <nav class=\"main-menu__nav\">
            <ul class=\"main-menu__list\">
                <li class=\"main-menu__list-item\">
                    <a class=\"main-menu__link main-menu__link--active\" href=\"\">Home</a>
                </li>
                <li class=\"main-menu__list-item\">
                    <a class=\"main-menu__link\" href=\"/about\">Over ons</a>
                </li>
                <li class=\"main-menu__list-item\">
                    <a class=\"main-menu__link\" href=\"\">Loopbaan</a>
                </li>
                <li class=\"main-menu__list-item\">
                    <a class=\"main-menu__link\" href=\"\">Perfectionisme</a>
                </li>
                <li class=\"main-menu__list-item\">
                    <a class=\"main-menu__link\" href=\"\">Hypnose</a>
                </li>
            </ul>
        </nav>
    </header>

    <div class=\"hero\" style=\"background-image: url('/img/hero.jpg')\">
        <div class=\"hero__gradient\"></div>
        <div class=\"hero__content\">
            ";
        // line 79
        $this->displayBlock('hero', $context, $blocks);
        // line 80
        echo "        </div>
    </div>

        ";
        // line 83
        $this->displayBlock('content', $context, $blocks);
        // line 84
        echo "
    <footer class=\"footer container\">
        <div class=\"logo footer__logo\">
            <img src=\"/img/biglogo.svg\" alt=\"\">
        </div>
        <nav class=\"footer__nav\">
            <ul class=\"footer__list\">
                <li class=\"footer__list-item\">
                    <a class=\"footer__link footer__link--active\" href=\"\">Home</a>
                </li>
                <li class=\"footer__list-item\">
                    <a class=\"footer__link\" href=\"\">Over ons</a>
                </li>
                <li class=\"footer__list-item\">
                    <a class=\"footer__link\" href=\"\">Loopbaan</a>
                </li>
                <li class=\"footer__list-item\">
                    <a class=\"footer__link\" href=\"\">Perfectionisme</a>
                </li>
                <li class=\"footer__list-item\">
                    <a class=\"footer__link\" href=\"\">Hypnose</a>
                </li>
            </ul>
        </nav>
        <ul class=\"footer__socials\">
            <li class=\"footer__social\">
                <a class=\"footer__social-link\" href=\"\" style=\"background-image: url('/img/fb.svg')\"></a>
            </li>
            <li class=\"footer__social\">
                <a class=\"footer__social-link\" href=\"\" style=\"background-image: url('/img/instagram.svg')\"></a>
            </li>
            <li class=\"footer__social\">
                <a class=\"footer__social-link\" href=\"\" style=\"background-image: url('/img/twitter.svg')\"></a>
            </li>
        </ul>
        <div class=\"footer__copy\">
            ©
            <span class=\"from-tablet\">
        Challenge Empowering Yourself 2020.
      </span>
            All rights reserved.
        </div>
    </footer>

    ";
        // line 128
        echo call_user_func_array($this->env->getFunction('foot')->getCallable(), []);
        echo "

</body>
</html>
";
    }

    // line 79
    public function block_hero($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 83
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    public function getTemplateName()
    {
        return "layouts/layout.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  192 => 83,  186 => 79,  177 => 128,  131 => 84,  129 => 83,  124 => 80,  122 => 79,  51 => 11,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "layouts/layout.twig", "D:\\invacto\\projects\\cms\\withfatfree\\example\\views\\layouts\\layout.twig");
    }
}
