<?php 
namespace App\Website;

use Psr\Container\ContainerInterface;
use Seriti\Tools\BASE_URL;
use Seriti\Tools\SITE_NAME;
use Seriti\Tools\URL_CLEAN;
use Seriti\Tools\URL_CLEAN_LAST;
use Seriti\Tools\Secure;
use Seriti\Tools\Menu;

//NB: only require if website has shopping or auction capabilities
use App\Shop\Helpers AS ShopHelpers;
use App\Auction\Helpers AS AuctionHelpers;


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
        //make true for modules you require
        $shop_setup = false;
        $payment_setup = false;
        $auction_setup = false;

        $user = $this->container->user;
        $db = $this->container->mysql;

        $module = $this->container->config->get('module','website');
        define('TABLE_PREFIX',$module['table_prefix']);
        $route_root = $module['route_root_page'];

        //TABLE_USER_EXTEND required for user registration
        if($shop_setup) {
           $module_shop = $this->container->config->get('module','shop');
           define('MODULE_SHOP',$module_shop);
           define('TABLE_USER_EXTEND',$module_shop['table_prefix'].'user_extend');
        }

        if($payment_setup) {
           $module_payment = $this->container->config->get('module','payment');
           define('MODULE_PAYMENT',$module_payment);
        }

        if($auction_setup) {
           $module_auction = $this->container->config->get('module','auction');
           define('MODULE_AUCTION',$module_auction);
           define('TABLE_USER_EXTEND',$module_auction['table_prefix'].'user_extend');
        }
       
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

        //will return false unless a user is logged in, and zone = ALL or PUBLIC and status <> HIDE
        $valid = $user->checkAccessRights($zone);
        
        //NB: this code is processed before LogoutController called 
        if($valid and URL_CLEAN !== $route_root.'logout') {
            //valid user logged in
            $menu_options['append'] = ['/public/logout'=>'Logout']; 

            $db->setAuditUserId($user->getId());
            
            //NB:If you enable this then links from other websites/servers will not work.
            //Secure::checkReferer(BASE_URL);

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
                $cart = ShopHelpers::getCart($db,MODULE_SHOP['table_prefix'],$temp_token);
                if($cart !==0 ) {
                    $no_items = '';
                    if($cart['item_count'] !==0 ) $no_items = $cart['item_count'];
                    $menu_options['icons'][] = ['id'=>'menu_cart','class'=>'menu_icon','url'=>'/public/cart','value'=>'<span class="glyphicon glyphicon-shopping-cart">'.$no_items.'</span>'];
                }  else {
                    //cart empty but needed for javascript display:inline when [Add to Order] and before next refresh 
                    $menu_options['icons'][] = ['id'=>'menu_cart','class'=>'menu_icon display_hidden','url'=>'/public/cart','value'=>'<span class="glyphicon glyphicon-shopping-cart"></span>'];
                }  
            }
        } 

        if($auction_setup) {
            $temp_token = $user->getTempToken(false);
            if($temp_token !== '') {
                $cart = AuctionHelpers::getCart($db,MODULE_AUCTION['table_prefix'],$temp_token);
                if($cart !==0 ) {
                    $no_items = '';
                    if($cart['item_count'] !==0 ) $no_items = $cart['item_count'];
                    $menu_options['icons'][] = ['id'=>'menu_cart','class'=>'menu_icon','url'=>'/public/cart','value'=>'<span class="glyphicon glyphicon-shopping-cart">'.$no_items.'</span>'];
                } else {
                    //cart empty but needed for javascript display:inline when [Add to Order] and before next refresh 
                    $menu_options['icons'][] = ['id'=>'menu_cart','class'=>'menu_icon display_hidden','url'=>'/public/cart','value'=>'<span class="glyphicon glyphicon-shopping-cart"></span>'];
                }   
            }
        }      
        
        //uncomment if you wish to have search facility next to logo in menu
        //$menu_options['search']['action'] = '/public/search';
        //$menu_options['search']['placeholder'] = 'Search site';

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
