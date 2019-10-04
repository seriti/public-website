<?php
namespace App\Website;

use Psr\Container\ContainerInterface;
use App\Website\Page;

class PageController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $param = [];

        $module = $this->container->config->get('module','website');
        $param['page_route_root'] = $module['route_root_page']; 

        $table_name = TABLE_PREFIX.'page'; 
        $table = new Page($this->container->mysql,$this->container,$table_name);

        $table->setup($param);
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'Public website pages';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}