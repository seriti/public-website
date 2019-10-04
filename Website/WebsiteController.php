<?php
namespace App\Website;

use Seriti\Tools\Menu;
use Seriti\Tools\BASE_URL;
//use Seriti\Tools\BASE_UPLOAD_WWW;

use Psr\Container\ContainerInterface;

use App\Website\Website;

//NB: Assumes ConfigPublic.php middleware class already invoked, should NOT be invoked within /admin/website   
class WebsiteController
{
    protected $container;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function __invoke($request, $response, $args)
    {
        $site = new Website($this->container->mysql,$this->container);
        $site->setup();

        $html = $site->process($args);

        $template['html'] = $html;
        //$template['sub_menu'] = $sub_menu;
        $template['javascript'] = $site->getJavascript();

        return $this->container->view->render($response,'public.php',$template);
    }
}