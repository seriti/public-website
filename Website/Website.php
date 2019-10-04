<?php 
namespace App\Website;

use Seriti\Tools\Form;
use Seriti\Tools\Secure;
use Seriti\Tools\Template;
use Seriti\Tools\Image;
use Seriti\Tools\Calc;
use Seriti\Tools\Menu;

use Seriti\Tools\DbInterface;
use Seriti\Tools\IconsClassesLinks;
use Seriti\Tools\MessageHelpers;
use Seriti\Tools\ContainerHelpers;
use Seriti\Tools\BASE_UPLOAD;
use Seriti\Tools\BASE_UPLOAD_WWW;
use Seriti\Tools\UPLOAD_DOCS;
use Seriti\Tools\BASE_PATH;
use Seriti\Tools\BASE_TEMPLATE;
use Seriti\Tools\BASE_URL;

use App\Shop\ProductList;
use App\Shop\AccountDashboard;
use App\Website\RegisterWizard;

use Psr\Container\ContainerInterface;

class Website 
{
    use IconsClassesLinks;
    use MessageHelpers;
    use ContainerHelpers;
   
    protected $container;
    protected $container_allow = ['user','system','cache','menu'];
    protected $user;

    protected $db;
    protected $debug = false;

    protected $mode = '';
    protected $errors = array();
    protected $errors_found = false; 
    protected $messages = array();

    protected $upload_dir = BASE_UPLOAD.UPLOAD_DOCS;
    protected $template_dir = BASE_TEMPLATE.'website/';
    //relative to http root
    protected $image_dir = BASE_URL.BASE_UPLOAD_WWW;
    

    public function __construct(DbInterface $db, ContainerInterface $container) 
    {
        $this->db = $db;
        $this->container = $container;
               
        if(defined('\Seriti\Tools\DEBUG')) $this->debug = \Seriti\Tools\DEBUG;
    }

    //use setup() for any custom configuration
    public function setup($param = [])
    {
        if(isset($param['template_dir'])) $this->template_dir = $param['template_dir'];
        if(isset($param['upload_dir'])) $this->upload_dir = $param['upload_dir'];
        if(isset($param['image_dir'])) $this->image_dir = $param['image_dir'];
        if(isset($param['debug'])) $this->debug = $param['debug'];

        $this->user = $this->getContainer('user');
    }

    //$param receives URL arguments
    public function process($param = [])
    {
        $page_valid = true;
        
        //get page id from url after "public/" base
        $link_url = $param['link_url'];
        $sql = 'SELECT page_id FROM '.TABLE_PREFIX.'page WHERE link_url = "'.$this->db->escapeSql($link_url).'" ';
        $page_id = $this->db->readSqlValue($sql,0);

        if($page_id == 0) {
            $sql = 'SELECT page_id,link_url FROM '.TABLE_PREFIX.'page WHERE status = "HOME" OR status = "OK" '.
                   'ORDER BY status, RAND() LIMIT 1 ';
            $home = $this->db->readSqlRecord($sql,0); 
            $page_id = $home['page_id'];
            $link_url = $home['link_url']; 
        }  

        if($page_id === 0) {
            $page_valid = false;
            $page = [];
            $page['type_id'] = 'STANDARD';
            $page['title'] = 'No pages setup yet!';
            $page['text_html'] = '<p>You have not configured any pages for your website yet.</p>';
        } else {
            $sql = 'SELECT page_id,title,page_access,text_html,type_id,status,meta_key,meta_title,meta_desc '.
                   'FROM '.TABLE_PREFIX.'page WHERE  page_id = "'.$this->db->escapeSql($page_id).'" ';
            $page = $this->db->readSqlRecord($sql); 
            if($page === 0) {
                $page_valid = false;
                unset($page);
                $page['type_id'] = 'STANDARD';
                $page['title'] = 'Invalid page requested['.$page_id.']!';
                $page['text_html'] = '<p>You have requested a page which does not exist.</p>';
            } elseif($page['status'] === 'HIDE') {
                $page_valid = false;
                $page['type_id'] = 'STANDARD';
                $page['title'] = 'HIDDEN page requested['.$page_id.']!';
                $page['text_html'] = '<p>You have requested a page is no longer available for public viewing.</p>';
            }  elseif($page['page_access'] !== 'NONE') {
                //verify user access level sufficient for page
                $page_valid = $this->user->checkUserAccess($page['page_access']);
            }    
        }

        //default page content
        $menu_html = '';
        $gallery_html = '';
        $image_html = '';
        $files_html = '';

        if(!$page_valid) {
            $page['type_id'] = 'STANDARD';
            $page['title'] = 'Invalid access';
            $page['text_html'] = 'You have attempted to access a page that has access restrictions in place. '.
                                 'Either you are not logged in or your access level is not sufficient. '.
                                 'Please contact us if you require further details.' ;
        } else {   

            //INSERT any specified custom content
            $this->insertPageContent($page);

            //get any images associated with page
            $sql = 'SELECT file_id,file_name,file_name_tn,title,description '.
                   'FROM '.TABLE_PREFIX.'files WHERE location_id = "WPI'.$this->db->escapeSql($page_id).'" ';
            $images = $this->db->readSqlArray($sql);
            if($images != 0) {
                if(count($images) == 1) {
                    
                    foreach($images as $image) {
                        $image_html .= '<img src="'.$this->image_dir.$image['file_name'].'" class="img-responsive center-block">';    
                    }  
                    
                } else {  
                    $options = array();
                    
                    if($page['type_id'] === 'GALLERY') {
                        $options['img_style'] = 'max-height:600px;';
                    } else {  
                        $options['img_style'] = 'max-height:300px";';
                    }
                    
                    
                    $type = 'CAROUSEL'; 
                    if($page['type_id'] === 'GALLERY_TN') $type = 'THUMBNAIL';
                    
                    $options['src_root'] = $this->image_dir;
                    $gallery_html = Image::buildGallery($images,$type,$options);
                    
                }  
                
            }   
            
            //get any documents associated with page
            $sql = 'SELECT file_id,file_name,file_name_tn,file_name_orig,file_size,title,description '.
                   'FROM '.TABLE_PREFIX.'files WHERE location_id = "WPF'.$this->db->escapeSql($page_id).'" ';
            $files = $this->db->readSqlArray($sql);
            if($files != 0) {
                $files_html .= '<h2>Download files:</h2>';
                $files_html .= '<ul>';
                foreach($files as $file_id => $file) {
                    $href = 'download?id='.$file_id;
                    $files_html .= '<li><a href="'.$href.'">'.
                                    '<span class="glyphicon glyphicon-cloud-download"></span>&nbsp;'.
                                     $file['file_name_orig'].'</a> <i>('.Calc::displayBytes($file['file_size']).')</i></li>';
                }  
                
                
                $files_html .= '</ul>';
            } 
            
             
        }  

        //get relevant template filename, allocate html and render
        switch($page['type_id']) {
            case 'STANDARD': $template_file = 'page.php'; break;
            case 'GALLERY': $template_file = 'gallery.php'; break;
            case 'GALLERY_TN': $template_file = 'gallery.php'; break;
            case 'DOWNLOAD': $template_file = 'download.php'; break;
            default: $template_file = 'page.php';
        }  

        $view = new Template($this->template_dir);
        $view->menu = $menu_html;
        $view->gallery = $gallery_html;
        $view->title = $page['title'];
        $view->text = $page['text_html'];
        $view->image = $image_html;
        $view->download = $files_html;
        $view->messages = $this->viewMessages();
                
        $html = $view->render($template_file);

        return $html;
    }

    protected function insertPageContent(&$page = []) 
    {
        if(strpos($page['title'],'{INSERT:SHOP_CATEGORY}') !== false or strpos($page['text_html'],'{INSERT:SHOP_PRODUCTS}') !== false) {

            $table_name = 'shp_product';
            $list = new ProductList($this->db,$this->container,$table_name);
            $list->setup([]);

            if(strpos($page['title'],'{INSERT:SHOP_CATEGORY}') !== false) {
                $insert_html = $list->viewSearchIndex('SELECT','category_id',$_POST);
                $page['title'] = str_replace('{INSERT:SHOP_CATEGORY}',$insert_html,$page['title']);
            }    
            
            if(strpos($page['text_html'],'{INSERT:SHOP_PRODUCTS}') !== false) {
                $insert_html = $list->processList();
                $page['text_html'] = str_replace('{INSERT:SHOP_PRODUCTS}',$insert_html,$page['text_html']);
            }
        }

        if(strpos($page['text_html'],'{INSERT:SHOP_DASHBOARD}') !== false) {
                
            $dashboard = new AccountDashboard($this->db,$this->container);
        
            $dashboard->setup();
            $insert_html = $dashboard->viewBlocks();

            $page['text_html'] = str_replace('{INSERT:SHOP_DASHBOARD}',$insert_html,$page['text_html']);
        }  

        if(strpos($page['text_html'],'{INSERT:REGISTER_WIZARD}') !== false) {
                
            $cache = $this->getContainer('cache');
            $user = $this->getContainer('user');
            //use temp token to identify user for duration of wizard
            $user_specific = false;
            $cache_name = 'Register_wizard'.$user->getTempToken();
            $cache->setCache($cache_name,$user_specific);

            $wizard_template = new Template(BASE_TEMPLATE);
            
            $wizard = new RegisterWizard($this->db,$this->container,$cache,$wizard_template);
            
            $wizard->setup();
            $insert_html = $wizard->process();

            $page['text_html'] = str_replace('{INSERT:REGISTER_WIZARD}',$insert_html,$page['text_html']);
        } 


    }

    public function getJavascript() 
    {
        $js = '';
        
        //$js .= '<script type="text/javascript">';

        //$js .= '</script>'; 
                      
        return $js;      
    }


}   

?>
