<?php
namespace App\Website;

use Psr\Container\ContainerInterface;
use App\Website\PageFile;

class PageFileController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table = TABLE_PREFIX.'files'; 
        $upload = new PageFile($this->container->mysql,$this->container,$table);

        $upload->setup();
        $html = $upload->processUpload();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'All Page Files';
        
        return $this->container->view->render($response,'admin_popup.php',$template);
    }
}