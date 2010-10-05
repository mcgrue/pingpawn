<?
    class AppController extends Controller {
        var $components = array(
            'Session',
            'SparkPlug.Authsome' => array('model' => 'User')
        );
        
        var $uses = array('SparkPlug.UserGroup');
        
        function beforeFilter() {
            parent::beforeFilter();
            //SparkPlugIt($this);
            $this->set('sessuser', Authsome::get());
        }
    }