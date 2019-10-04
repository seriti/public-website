<?php
namespace App\Website;

use App\Website\Menu;
use Psr\Container\ContainerInterface;

class MenuController
{
    protected $container;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function __invoke($request, $response, $args)
    {
        $param=['row_name'=>'menu-item',
                'col_label'=>'title'];

        $module = $this->container->config->get('module','website');        
        $table = $module['table_prefix'].'menu';

        $tree = new Menu($this->container->mysql,$this->container,$table);
        
        $tree->setup($param);
        $html = $tree->processTree();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'Public Website Menu';
        $template['javascript'] = $tree->getJavascript();
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}