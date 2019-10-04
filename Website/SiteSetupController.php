<?php
namespace App\Website;

use App\Website\SiteSetup;
use Psr\Container\ContainerInterface;

class SiteSetupController
{
    protected $container;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function __invoke($request, $response, $args)
    {
        $module = $this->container->config->get('module','website');  

        $setup = new SiteSetup($this->container->mysql,$this->container,$module);
               
        $setup->setup();
        $html = $setup->processSetup();

        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'Public Website setup';

        return $this->container->view->render($response,'admin.php',$template);
    }
}