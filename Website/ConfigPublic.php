<?php 
namespace App\Website;

use Psr\Container\ContainerInterface;
use Seriti\Tools\BASE_URL;
use Seriti\Tools\SITE_NAME;
use Seriti\Tools\URL_CLEAN;
use Seriti\Tools\URL_CLEAN_LAST;
use Seriti\Tools\Secure;
use Seriti\Tools\Menu;

//NB: only require if website has shopping capabilities
use App\Shop\Helpers AS ShopHelpers;

class ConfigPublic
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
        $shop_setup = true;     
        $user = $this->container->user;
        $db = $this->container->mysql;

        $module = $this->container->config->get('module','website');
        define('TABLE_PREFIX',$module['table_prefix']);

        if($shop_setup) {
           $module_shop = $this->container->config->get('module','shop');
           define('TABLE_PREFIX_SHOP',$module_shop['table_prefix']); 
        }
        
        $route_root = $module['route_root_page'];

        //NB: Public menu must be setup independently of container menu which is for /admin
        $menu_god = []; 
        $menu_options = [];
        $table = TABLE_PREFIX.'menu';
        $menu = new Menu($db,$this->container,$table);
        //NB only menu items with 'NONE' as access setting will be clickable if user not logged in.
        $setup_param = ['check_access'=>true]; 
        $menu->setup($setup_param);

       
        //default access levels=['GOD','ADMIN','USER','VIEW']
        $redirect_route = $route_root.'home'; //could leave out 'home' and redirect in src/routes.php 
        $minimum_level = 'VIEW';
        $zone = 'PUBLIC';
        //will return false unless a user is logged in with access >= minimum level and zone = ALL or PUBLIC and status <> HIDE
        $valid = $user->checkAccessRights($zone);
        
        //NB: this code is processed before LogoutController called 
        if($valid and URL_CLEAN !== $route_root.'logout') {
            //valid user logged in
            $menu_options['append'] = ['/public/logout'=>'Logout']; 

            $db->setAuditUserId($user->getId());
            Secure::checkReferer(BASE_URL);
            //user access level must be valid and >= minimum level
            $valid = $user->checkUserAccess($minimum_level);

            //check current menu route is valid for user based on menu settings
            //NB: individual pages also have access settings as these may not be in menu
            if($valid) $valid = $menu->checkRouteAccess(URL_CLEAN);

            //delete user session,tokens,cookies and send to home page
            if(!$valid) {
                $user->manageUserAction('LOGOUT');
                return $response->withRedirect('/'.$redirect_route);
            }    
        } else {
            //no user logged in
            $menu_options['append'] = ['/public/register'=>'Register','/login'=>'Login']; 
        }  

        //NB: only required for shopping cart link
        if($shop_setup) {
            $temp_token = $user->getTempToken(false);
            if($temp_token !== '') {
                $cart = ShopHelpers::getCart($db,TABLE_PREFIX_SHOP,$temp_token);
                if($cart !==0 ) {
                    $no_items = '';
                    if($cart['item_count'] !==0 ) $no_items = $cart['item_count'];
                    $menu_options['append']['/public/cart'] = '<span class="glyphicon glyphicon-shopping-cart">'.$no_items.'</span>';
                }    
            }
        }    
        
        //menu logo, defined in setup_app.php
        if(defined('WWW_MENU_LOGO')) $logo = WWW_MENU_LOGO; else $logo = '';

        $menu_options['show_disabled'] = false; //prevent showing menu items where insufficient access
        $menu_options['logo_link'] = BASE_URL.$redirect_route;
        $menu_options['active_link'] = URL_CLEAN;
        $menu_options['logo'] = $logo;
        
        $menu_options['style'] = WWW_MENU_STYLE;
        //prevent $menu_god merge with /admin SYSTEM_MENU constant
        $menu_options['merge_system'] = false;
        $menu_html = $menu->buildMenu($menu_god,$menu_options);

        $this->container->view->addAttribute('menu',$menu_html);
      
        
        $response = $next($request, $response);
        
        return $response;
    }
}