<?php
namespace App\Website;

use Seriti\Tools\Menu;
use Seriti\Tools\BASE_URL;
//use Seriti\Tools\BASE_UPLOAD_WWW;

use Psr\Container\ContainerInterface;

//NB: Assumes ConfigPublic.php middleware class already invoked, should NOT be invoked within /admin/website   
class LogoutController
{
    protected $container;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function __invoke($request, $response, $args)
    {
        $user = $this->container->user;
        $user->manageUserAction('LOGOUT');
        
        //you could redirect user anywhere at this point if you wish
        $html = '<h1>You have logged out successfully, goodbye.</h1>';

        $template['html'] = $html;
        //$template['sub_menu'] = $sub_menu;
        //$template['javascript'] = $site->getJavascript();

        return $this->container->view->render($response,'public.php',$template);
    }
}