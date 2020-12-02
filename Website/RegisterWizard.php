<?php
namespace App\Website;

use Exception;

use Seriti\Tools\Wizard;
use Seriti\Tools\Date;
use Seriti\Tools\Form;
use Seriti\Tools\Doc;
use Seriti\Tools\Calc;
use Seriti\Tools\Secure;
use Seriti\Tools\Plupload;
use Seriti\Tools\STORAGE;
use Seriti\Tools\BASE_UPLOAD;
use Seriti\Tools\UPLOAD_TEMP;
use Seriti\Tools\UPLOAD_DOCS;
use Seriti\Tools\TABLE_USER;

//use App\Clients\Helpers;

class RegisterWizard extends Wizard 
{
    protected $user;

    //configure
    public function setup($param = []) 
    {
        $this->user = $this->getContainer('user');

        $param['bread_crumbs'] = true;
        $param['strict_var'] = false;
        $param['csrf_token'] = $this->user->getTempToken();
        parent::setup($param);

        //standard user cols
        $this->addVariable(array('id'=>'name','type'=>'STRING','title'=>'Your name','required'=>true));
        $this->addVariable(array('id'=>'email','type'=>'EMAIL','title'=>'Your email address'));
        $this->addVariable(array('id'=>'email_repeat','type'=>'EMAIL','title'=>'Your email address repeated'));
        
        //additional user information
        $this->addVariable(array('id'=>'name_invoice','type'=>'STRING','title'=>'Your invoice name','required'=>true));
        $this->addVariable(array('id'=>'cell','type'=>'STRING','title'=>'Your cellphone number','required'=>true));
        $this->addVariable(array('id'=>'tel','type'=>'STRING','title'=>'Your landline number','required'=>false));
        $this->addVariable(array('id'=>'address','type'=>'TEXT','title'=>'Your postal address','required'=>false));
        $this->addVariable(array('id'=>'email_alt','type'=>'EMAIL','title'=>'Alternative Email address','required'=>false));
        
        //define pages and templates
        $this->addPage(1,'Register with us','website/register_wizard_start.php',['go_back'=>false]);
        $this->addPage(2,'Capture other contact details','website/register_wizard_detail.php');
        $this->addPage(3,'Registration complete','website/register_wizard_final.php',['final'=>true]);    

    }

    public function processPage() 
    {
        $error = '';
        $error_tmp = '';

        //NB: prevent registration for a logged in user. Note that after first page new user is already registered!;
        if(!$this->data['user_created'] and $this->user->getId() != 0) {
            $error = 'USER_REGISTRATION_ERROR: Another user is linked to this device.';
            if($this->debug) $error .= 'User ID['.$this->user->getId().'] and email['.$this->user->getEmail().'] already logged in.';
            throw new Exception($error);
        }
                
        //PROCESS create new user with public access
        if($this->page_no == 1) {

            if(!$this->data['user_created']) {
                $name = $this->form['name'];
                $email = $this->form['email'];
                $email_repeat = $this->form['email_repeat'];
                            
                if($email !== $email_repeat) $this->addError('Email repeat['.$email_repeat.'] not same as email['.$email.']');
                
                $sql = 'SELECT * FROM '.TABLE_USER.' '.
                       'WHERE '.$this->user_cols['email'].' = "'.$this->db->escapeSql($email).'" ';
                $existing_user = $this->db->readSqlRecord($sql);
                if($existing_user !== 0) {
                   $this->addError('Email ['.$email.'] already allocated to another user.'); 
                }       

                if(!$this->errors_found) {
                    $password = Form::createPassword();
                    $access = 'USER';
                    $zone = 'PUBLIC';
                    $status = 'NEW';

                    $this->user->createUser($name,$email,$password,$access,$zone,$status,$error_tmp);
                    if($error_tmp !== '') {
                        $this->addError($error_tmp);
                    } else {
                        $user = $this->user->getUser('EMAIL',$email);
                        $remember_me = true;
                        $days_expire = 30;
                        $this->user->manageUserAction('LOGIN_REGISTER',$user,$remember_me,$days_expire);
                        
                        $this->data['user_created'] = true;
                        $this->data['password'] = $password;
                        $this->data['user_id'] = $user[$this->user_cols['id']];
                        //default for next page
                        $this->form['name_invoice'] = $name;
                    }
                }
            }    
            
        } 
        
        //PROCESS additional info required
        if($this->page_no == 2) {
            //defined in ConfigPublic depending on active module
            $table_extend = TABLE_USER_EXTEND;

            $data = [];
            $data['user_id'] = $this->data['user_id'];
            $data['name_invoice'] = $this->form['name_invoice'];
            $data['cell'] = $this->form['cell'];
            $data['tel'] = $this->form['tel'];
            $data['email_alt'] = $this->form['email_alt'];
            $data['ship_address'] = $this->form['address'];
            $data['bill_address'] = $this->form['address'];

            $extend = $this->db->getRecord($table_extend,['user_id'=>$data['user_id']]);
            if($extend === 0) {
                $this->db->insertRecord($table_extend,$data,$error_tmp );
            } else {
                unset($data['user_id']);
                $where = ['extend_id' => $extend['extend_id']];
                $this->db->updateRecord($table_extend,$data,$where,$error_tmp );
            }

            if($error_tmp !== '') {
                $error = 'We could not save your details.';
                if($this->debug) $error .= $error_tmp;
                $this->addError($error);
            }    
        }  
        
        //final page display only, no processing required
        if($this->page_no == 3) {
               
          
        }  
    }
}

?>


