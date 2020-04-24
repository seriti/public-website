<?php 
namespace App\Website;

use Seriti\Tools\Table;
use Seriti\Tools\Listing;

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
use Seriti\Tools\UPLOAD_DOCS;
use Seriti\Tools\BASE_PATH;
use Seriti\Tools\BASE_TEMPLATE;
use Seriti\Tools\BASE_URL;

use Seriti\Tools\BASE_UPLOAD_WWW;

use Psr\Container\ContainerInterface;

class SiteList extends Listing
{
    
    //configure
    public function setup($param = []) 
    {

        $param = ['row_name'=>'Page','col_label'=>'title', 'image_pos'=>'LEFT','format'=>'MERGE_COLS']; //'format'=>'MERGE_COLS'
        parent::setup($param);

        $this->addListCol(array('id'=>'page_id','type'=>'INTEGER','title'=>'Page ID','key'=>true,'key_auto'=>true,'list'=>false));
        //$this->addListCol(array('id'=>'type_id','type'=>'STRING','title'=>'Page type'));
        $this->addListCol(array('id'=>'title','type'=>'STRING','title'=>'Title','class'=>'list_item_title'));
        $this->addListCol(array('id'=>'text_html','type'=>'TEXT','title'=>'Text','class'=>'list_item_text'));
        //$this->addListCol(array('id'=>'meta_title','type'=>'STRING','title'=>'SEO-Meta title','max'=>250,'required'=>false));
        //$this->addListCol(array('id'=>'meta_key','type'=>'STRING','title'=>'SEO-Meta keywords','max'=>250,'required'=>false));
        //$this->addListCol(array('id'=>'meta_desc','type'=>'STRING','title'=>'SEO-Meta description','max'=>250,'required'=>false));
        //$this->addListCol(array('id'=>'status','type'=>'STRING','title'=>'Status','hint'=>'You can have multiple HOME pages which are randomly selected from'));

        $this->addSortOrder('T.status,T.type_id,T.title','Status, Type then Title','DEFAULT');

        //$this->addListAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        
        $this->addSearch(array('title','text_html'),array('rows'=>1));

        $this->addSelect('status','(SELECT "OK") UNION (SELECT "HOME") UNION (SELECT "HIDE")');
       

        $this->setupListImages(array('table'=>TABLE_PREFIX.'files','location'=>'WPI','max_no'=>100,'manage'=>false,
                                     'list'=>true,'list_no'=>1,'storage'=>'local','path'=>BASE_UPLOAD_WWW,
                                     'link_url'=>'page_image','link_data'=>'SIMPLE','width'=>'700','height'=>'600'));

        /*                          
        $this->setupListFiles(array('table'=>TABLE_PREFIX.'files','location'=>'WPF','max_no'=>100,
                                'icon'=>'<span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>&nbsp;&nbsp;manage',
                                'list'=>false,'list_no'=>1,'storage'=>STORAGE_WWW,
                                'link_url'=>'page_file','link_data'=>'SIMPLE','width'=>'700','height'=>'600'));
        */

        
    }

    protected function modifyRowValue($col_id,$data,&$value) 
    {
        if($col_id === 'type_id') {
            $test_link = '<a href="/?page='.$data['page_id'].'" target="_blank">'.
                         '<span class="glyphicon glyphicon-eye-open"></span>&nbsp;test</a>';
            $value = $test_link.': '.PAGE_TYPE[$value];
        }  
        
        if($col_id === 'page_id') {
            $value = $value.': [link](?page='.$value.')';
        }  
    } 
  
   
    
}  

?>
