<?php 
namespace App\Website;

use Seriti\Tools\Table;
use Seriti\Tools\Form;
use Seriti\Tools\Secure;
use Seriti\Tools\Validate;
use Seriti\Tools\Html;
use Seriti\Tools\BASE_UPLOAD_WWW;

class Page extends Table 
{
    
    //NB: this is base route for all page links 
    protected $route_root_page = '';
    protected $reserved_routes = ['login','logout','ajax'];

    //configure
    public function setup($param = []) 
    {
        $parent_param = ['row_name'=>'Page','col_label'=>'title'];
        parent::setup($parent_param);

        if(isset($param['route_root_page'])) $this->route_root_page = $param['route_root_page'];

        $config = $this->getContainer('config');
        //'NONE' for access is default and means no user needs to be logged in
        $access = $config->get('user','access');
        $access[] = 'NONE';

        $this->info['EDIT'] = 'You can use markdown and or raw html in page text field. '.
                              'The <a href="https://www.markdownguide.org/basic-syntax" target="_blank">markdown</a> interpreter is '.
                              '<a href="http://parsedown.org" target="_blank">Parsedown</a> and this allows you to simply create many '.
                              'standard html elements like headings,lists,bold,italic,underline and also more complex layouts like tables.'.
                              'After any changes you need to click [submit] button at bottom of form to save changes. ';
        $this->info['LIST'] = 'Click the Test link for any page to view it as will appear in public. '.
                              'NB: Until a page is linked to from another page or added to the site menu it is invisible to a public user. '.
                              'Once a page has been created you can upload one or more images and documents to associate with it. '.
                              'If a single image is added, then it is displayed in left column, otherwise the images are added to a gallery. '.
                              'Try the various page types to see how layout changes. '.
                              'Use the "[link]?page=X" markdown in Page ID column in any page text to link to that page from another page.';

        //widens value column
        $this->classes['col_value'] = 'col-sm-9 col-lg-10 edit_value';
        
        $this->addTableCol(array('id'=>'page_id','type'=>'INTEGER','title'=>'Page ID','key'=>true,'key_auto'=>true,'list'=>true));
        $this->addTableCol(array('id'=>'type_id','type'=>'STRING','title'=>'Page type'));
        $this->addTableCol(array('id'=>'title','type'=>'STRING','title'=>'Page title','hint'=>'This appears at top of the page in large font'));
        $this->addTableCol(array('id'=>'page_access','type'=>'STRING','title'=>'Access required','required'=>true,'new'=>'NONE','hint'=>'Set minimium user access level required'));
        $this->addTableCol(array('id'=>'text_markdown','type'=>'TEXT','secure'=>false,'title'=>'Page text','rows'=>20,
                            'hint'=>'Uses <a href="http://parsedown.org/tests/" target="_blank">parsedown</a> extended <a href="https://www.markdownguide.org/basic-syntax" target="_blank">markdown</a> format, or raw html','list'=>false));
        $this->addTableCol(array('id'=>'meta_title','type'=>'STRING','title'=>'SEO-Meta title','max'=>250,'required'=>true));
        //NB: this is used for url creation and must not be changed
        $this->addTableCol(array('id'=>'link_url','type'=>'STRING','title'=>'URL link','max'=>250,'required'=>true,'edit'=>false));
        $this->addTableCol(array('id'=>'meta_key','type'=>'STRING','title'=>'SEO-Meta keywords','max'=>250,'required'=>false,'list'=>false));
        $this->addTableCol(array('id'=>'meta_desc','type'=>'STRING','title'=>'SEO-Meta description','max'=>250,'required'=>false,'list'=>false));
        $this->addTableCol(array('id'=>'status','type'=>'STRING','title'=>'Status','hint'=>'You can have multiple HOME pages which are randomly selected from'));

        $this->addSortOrder('T.status,T.type_id,T.title','Status, Type then Title','DEFAULT');

        $this->addAction(array('type'=>'check_box','text'=>''));
        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        //$this->addAction(array('type'=>'view','text'=>'view','icon_text'=>'view'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));

        $this->addSearch(array('type_id','title','text_markdown','status','meta_title','meta_key','meta_desc'),array('rows'=>3));

        $this->addSelect('status','(SELECT "OK") UNION (SELECT "HOME") UNION (SELECT "HIDE")');
        $this->addSelect('type_id',array('list'=>PAGE_TYPE));
        $this->addSelect('page_access',array('list'=>$access,'list_assoc'=>false,'invalid'=>'NONE_SELECTED')); //default is 'NONE' for no selection


        $this->setupImages(array('table'=>TABLE_PREFIX.'files','location'=>'WPI','max_no'=>100,
                                  'icon'=>'<span class="glyphicon glyphicon-picture" aria-hidden="true"></span>&nbsp;manage',
                                  'list'=>true,'list_no'=>1,'storage'=>STORAGE_WWW,'path'=>BASE_UPLOAD_WWW,
                                  'link_page'=>'page_image','link_data'=>'SIMPLE','width'=>'700','height'=>'600'));

                                  
        $this->setupFiles(array('table'=>TABLE_PREFIX.'files','location'=>'WPF','max_no'=>100,
                                'icon'=>'<span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>&nbsp;&nbsp;manage',
                                'list'=>true,'list_no'=>1,'storage'=>STORAGE_WWW,
                                'link_page'=>'page_file','link_data'=>'SIMPLE','width'=>'700','height'=>'600'));
        

        
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
  
    protected function beforeUpdate($id,$context,&$data,&$error) 
    {
        //construct link url from first 64 chars odf meta title, and must be unique.
        if($context === 'INSERT') {
            $link_url = strtolower($data['meta_title']);
            $link_url = str_replace(' ','-',$link_url);
            $link_url = Secure::clean('alpha',$link_url);
            $link_url = substr($link_url,0,64);

            $sql='SELECT page_id,title FROM  '.TABLE_PREFIX.'page '.
                 'WHERE link_url = "'.$this->db->escapeSql($link_url).'" ';
                 //'WHERE page_id = "'.$this->db->escapeSql($id).'"';
            $page = $this->db->readSqlRecord($sql);
            if($page != 0) {
                $error .= 'Page link url['.$link_url.'] derived from first 64 characters of Meta title '.
                          'is being used by Another page ID['.$page['page_id'].'] & Title['.$page['title'].']. '.
                          'Please modify Meta title to be unique.';                           
            } else {
                $data['link_url'] = $link_url;

                if(array_search($link_url,$this->reserved_routes) !== false) {
                    $error .= 'SEO-Meta title cannot be a reserved route['.implode(',',$this->reserved_routes).']';
                }
            }


        }  
    }

    protected function afterUpdate($id,$context,$data) 
    {
        //converts page markdown into html and save 
        $text = $data['text_markdown'];
        if($text !== '') {
            //first need to convert  [link](?page=6) to  [link](public/link_url)
            $sql = 'SELECT page_id,link_url FROM '.TABLE_PREFIX.'page ';
            $links = $this->db->readSqlList($sql);
            foreach($links as $page_id=>$link_url) {
                $search = '?page='.$page_id;
                $replace = '/'.$this->route_root_page.$link_url;
                $text = str_replace($search,$replace,$text);
            }

            //now convert any markdown to html
            $html = Html::markdownToHtml($text);      
            $sql='UPDATE '.TABLE_PREFIX.'page SET text_html = "'.$this->db->escapeSql($html).'" '.
                 'WHERE page_id = "'.$this->db->escapeSql($id).'"';
            $this->db->executeSql($sql,$error_tmp);
        }

        
    }  
    
}
?>
