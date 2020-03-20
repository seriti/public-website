<?php
namespace App\Website;

use Psr\Container\ContainerInterface;
use App\Website\Help;

class HelpController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $module = $this->container->config->get('module','website');        
        $table = $module['table_prefix'].'help';
        
        $table = new Help($this->container->mysql,$this->container,$table);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'Public Help Content';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}