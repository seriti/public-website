<?php
namespace App\Website;

use Psr\Container\ContainerInterface;
use App\Website\PublicHelp;

class PublicHelpController
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
        $help = new PublicHelp($this->container->mysql,$this->container,$table);
        
        $html = $help->getHelp();
        
        $template['html'] = $html;
        $template['title'] =  'Help topics';
        
        return $this->container->view->render($response,'public.php',$template);
    }
}