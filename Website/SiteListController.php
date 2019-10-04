<?php
namespace App\Website;

use Seriti\Tools\Menu;
use Seriti\Tools\BASE_URL;
use Seriti\Tools\BASE_UPLOAD_WWW;

use Psr\Container\ContainerInterface;

use App\Website\SiteList;

class SiteListController
{
    protected $container;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function __invoke($request, $response, $args)
    {
        /*
        $module = $this->container->config->get('module','website');
        define('TABLE_PREFIX',$module['table_prefix']);

        $table = $module['table_prefix'].'menu';
        $menu = new Menu($this->container->mysql,$this->container,$table);
        $param = ['check_access'=>false]; //no user access
        $menu->setup($param);

        $logo = $this->container['system']->getDefault('WWW_MENU_IMAGE','');
        if($logo !== '') $logo = '<img src="'.BASE_URL.BASE_UPLOAD_WWW.$logo.'" height="40">';

        $system = []; //can specify any GOD access system menu items
        $options['logo_link'] = BASE_URL;
        $options['active_link'] = '/page='.$args['page'];
        $options['logo'] = $logo;
        $options['logout'] = '';
        $menu_html = $menu->buildMenu($system,$options);
        */

        define('TABLE_PREFIX','www_');

        $table_name = TABLE_PREFIX.'page';
        $list = new SiteList($this->container->mysql,$this->container,$table_name);
        $list->setup();


        $html = $list->processList();

        //return $response->write($html);
        
        $template['html'] = $html;
        $template['title'] = 'Public website listy wisty';
        //$template['javascript'] = $dashboard->getJavascript();

        return $this->container->view->render($response,'admin.php',$template);
        
    }
}