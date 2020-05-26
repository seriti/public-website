<?php
namespace App\Website;

use Psr\Container\ContainerInterface;
use App\Website\PageFile;

use Seriti\Tools\Secure;

class PageDownloadController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table = 'www_files'; 
        $upload = new PageFile($this->container->mysql,$this->container,$table);

        $_GET['mode'] = 'download';

        $upload->setup();
        $html = $upload->processUpload();
        
        $template['html'] = $html;
        $template['title'] = 'Document download';
        
        return $this->container->view->render($response,'public.php',$template);
    }
}