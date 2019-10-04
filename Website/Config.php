<?php 
namespace App\Website;

use Psr\Container\ContainerInterface;
use Seriti\Tools\BASE_URL;
use Seriti\Tools\SITE_NAME;

class Config
{
    
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

    }

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        
        $module = $this->container->config->get('module','website');
        $menu = $this->container->menu;
        
        //need local copies of files for website
        define('STORAGE_WWW','local');
        define('TABLE_PREFIX',$module['table_prefix']);
        define('MODULE_ID','WEBSITE');
        define('MODULE_LOGO','<img src="'.BASE_URL.'images/customise40.png"> ');
        define('MODULE_PAGE',URL_CLEAN_LAST);
        define('PAGE_TYPE',['STANDARD'=>'Standard page',
                            'GALLERY'=>'Image gallery carousel',
                            'GALLERY_TN'=>'Image gallery thumbnails']);
        
        //define('MODULE_NAV',$menu->buildNav($module['route_list'],MODULE_PAGE));
        $submenu_html = $menu->buildNav($module['route_list'],MODULE_PAGE);
        $this->container->view->addAttribute('sub_menu',$submenu_html);
       
        $response = $next($request, $response);
        
        return $response;
    }
}