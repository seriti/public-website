<?php 
namespace App\Website;

use Seriti\Tools\Upload;
use Seriti\Tools\BASE_PATH;
use Seriti\Tools\BASE_UPLOAD_WWW;

class PageFile extends Upload 
{
  //configure
    public function setup($param = []) 
    {
        $file_prefix = 'WWW'; 
        $id_prefix = 'WPF'; //WebsitePageFile

        $param = ['row_name'=>'Page document',
                  'pop_up'=>true,
                  'col_label'=>'file_name_orig',
                  'update_calling_page'=>true,
                  'storage'=>STORAGE_WWW,
                  'upload_path_base'=>BASE_PATH,
                  'upload_path'=>BASE_UPLOAD_WWW,
                  'prefix'=>$file_prefix];
        parent::setup($param);

        $param = [];
        $param['table']     = TABLE_PREFIX.'page';
        $param['key']       = 'page_id';
        $param['label']     = 'title';
        $param['child_col'] = 'location_id';
        $param['child_prefix'] = $id_prefix ;
        $param['show_sql'] = 'SELECT CONCAT("Page: ",title) FROM '.TABLE_PREFIX.'page WHERE page_id = "{KEY_VAL}"';
        $this->setupMaster($param);

        $this->addAction('delete');

        //$access['read_only'] = true;                         
        //$this->modifyAccess($access); p
    }
}
?>
