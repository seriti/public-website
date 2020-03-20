<?php
namespace App\Website;

use Psr\Container\ContainerInterface;
use Seriti\Tools\Secure;


class Ajax
{
    protected $container;
    protected $db;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $this->container->mysql;
    }


    public function __invoke($request, $response, $args)
    {
        $mode = '';
        $output = '';

        if(isset($_GET['mode'])) $mode = Secure::clean('basic',$_GET['mode']);

        if($mode === 'menu') $output = $this->getMenuRoutes();

        return $output;
    }

    protected function getMenuRoutes()
    {
        $output = '';

        $menu_type = Secure::clean('string',$_POST['menu_type']);
        //$menu_type = 'LINK_CUSTOM';

        $module = $this->container->config->get('module','website');  

        $sql = 'SELECT CONCAT("'.$module['route_root_page'].'",link_url),title FROM '.TABLE_PREFIX.'page '.
               'WHERE status = "OK" OR status = "HOME" ORDER BY title';
        $my_pages = $this->db->readSqlList($sql);
          
        $links = [];
        
        switch($menu_type) {
            case 'LINK_PAGE':
                if($my_pages==0) {
                    $links['#']='NO pages created!';
                } else {  
                    $links = $my_pages;
                }  
                break;
            case 'LINK_SYSTEM':
                //$links['public/home'] = 'Home dashboard';
                $links['public/help'] = 'Help';
                break;
            case 'LINK_ACCOUNT':
                $links[$module['route_root_account'].'dashboard'] = 'Account dashboard';
                $links[$module['route_root_account'].'profile'] = 'Account profile';
                $links[$module['route_root_account'].'orders'] = 'Account orders';
                $links[$module['route_root_account'].'payments'] = 'Account payments';
                break;    
             
        }
                    
        if(count($links) === 0) {
            $output = 'ERROR';
        } else {
            $output = json_encode($links);    
        }    

        return $output;

    }
}