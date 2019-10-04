<?php
namespace App\Website;

use App\Website\SeoSetup;
use Psr\Container\ContainerInterface;

class SeoSetupController
{
    protected $container;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function __invoke($request, $response, $args)
    {
        $module = $this->container->config->get('module','website');  

        $setup = new SeoSetup($this->container->mysql,$this->container,$module);
               
        $setup->setup();
        $html = $setup->processSetup();

        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'Public Website SEO';

        return $this->container->view->render($response,'admin.php',$template);
    }
}