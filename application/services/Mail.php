<?php
/**
 * Send Mail
 * see http://www.wowww.ch/index.php?post/2009/03/16/Zend-Mail-avec-template-standardise-avec-Zend-Layout-et-Zend-View
 **/
class Sos_Service_Mail extends Zend_Mail {
	protected $_view ;
	protected $_layout ;
	
	/**
	 * @var Zend_Config Configuration object
	 */
	protected $_configuration ;

	/**
	 * @var string chemin vers les scripts de vue
	 */
	protected $_path ;

	/**
	 * Constructeur
	 *
	 * @param string $path pwth where we stroe the view
	 */
	public function __construct($user=null,$path = null, $charset = 'UTF-8', $from=NULL, $fromName='') {
		// construction de Zend_Mail
		parent::__construct($charset) ;

		if ($path === null) {
			// cuurent path view layout
			$path = Zend_Layout::getMvcInstance()->getViewScriptPath() ;
			
			$path .= "/mail" ;
			
			// erreur si le chemin n'existe pas
			if (!file_exists($path))	{
				throw new Zend_Exception("Cannot determine the mail script path, $path does not exist") ;
			}
		}

		$this->_path = $path ;

		//view on the view path
		$this->_view = new Zend_View() ;
		$this->_view->setBasePath($this->_path) ;

		// load helper like a basic view
		$helper_paths = Zend_Layout::getMvcInstance()->getView()->getHelperPaths() ;
		foreach ($helper_paths as $prefix => $paths)	{
			foreach ($paths as $path) {
				$this->_view->addHelperPath($path, $prefix) ;
			}
		}

		// path with layout
		$this->_layout = new Zend_Layout() ;
		$this->_layout->setLayoutPath($this->_path );

		//load tempalting information
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/email.ini', 'emailtempalting');
		$this->_configuration = $config->mail ;
		$this->_view->rooturl = $this->_configuration-> rooturl; 
		$this->_layout->rooturl = $this->_configuration-> rooturl;
		if(is_null($from)){
			if (isset($this->_configuration->from))	{
				$from = $this->_configuration->from ;
					
				if (isset($from->address))	{
					//default from
					$this->setFrom($from->address, isset($from->name) ? $from->name : null);
				}
			}
		}
		else{
			$this->setFrom($from, $fromName);
		}
		 
	}

	public function sendwithtransport()
	{
	$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/email.ini', 'gmail');
		//send mail
		$cfg = array(
			'ssl' => 'tls',
			'port' => 587,
			'auth' => 'login',
			'username' => $config->resources->email->account,
			'password' => $config->resources->email->password
		);
		$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $cfg);
		$this->send($transport);
	}
	/**
	 * Subject with the format
	 * My Project > %s
	 */
	public function setSubject($subject, $format = true)	{
		if ($format && isset($this->_configuration->subject) && isset($this->_configuration->subject->format))	{
			$subject = sprintf($this->_configuration->subject->format, $subject) ;
		}

		return parent::setSubject($subject) ;
	}
	
	/**
	 * get the view if needed
	 */
	public function getView() {
		return $this->_view ;
	}

	/**
	 * get the layout if needed
	 */
	public function getLayout() {
		return $this->_layout ;
	}

	
	
	/**
	 * set where is the text layout to call with the setBodyText
	 */
	public function setScriptText($script, $layout = null, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
		// on génère la vue
		
		$content = $this->_view->render($script);

		if ($layout === null) {
			// approve  
			if (!isset($this->_configuration->layout) || !isset($this->_configuration->layout->text)) {
				throw new Zend_Exception('No layout specified for the text view') ;
			}
				
			$layout = $this->_configuration->layout->text ;
		}

		// on défini le layout spécifié
		$this->_layout->setLayout($layout);
		// on défini le contenu du layout (la vue texte générée)
		$this->_layout->content = $content;

		// on fait un gros mélange du tout
		$body = $this->_layout->render() ;

		// on passe le résultat dans la méthode de Zend_Mail
		return $this->setBodyText($body, $charset, $encoding) ;
	}

	/**
	 * set where is the text layout to call with the setBodyHtml
	 */
	public function setScriptHtml($script, $layout = null, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
		$content = $this->_view->render($script);

		if ($layout === null) {
			if (!isset($this->_configuration->layout) || !isset($this->_configuration->layout->html)) {
				throw new Zend_Exception('No layout specified for the HTML view') ;
			}
				
			$layout = $this->_configuration->layout->html ;
		}

		$this->_layout->setLayout($layout);
		$this->_layout->content = $content;

		$body = $this->_layout->render() ;

		return $this->setBodyHtml($body, $charset, $encoding) ;
	}

	/**
	 * Méthode proxy vers les variables membres de la vue afin
	 * de pouvoir définir le contenu en faisant $myMail->monContenu = 'truc'
	 */
	public function __set($key, $val)	{
		if ('_' != substr($key, 0, 1)) {
			$this->_view->$key = $val;
			return;
		}
	}

	/**
	 * Défini la valeur du To: dans l'e-mail et efface le contenu
	 * de la variable courante.
	 */
	public function setTo($email, $name='') {
		error_log("send mail to ".$email);
		unset($this->_headers['To']) ;
		$this->addTo($email, $name) ;
		return $this ;
	}
	
	/**
	 * Add reply to address
	 */
	public function AddReplyTo($email, $name = "") {
        $this->addHeader('Reply-to', $email, $name);
    }

	/**
	 * Défini le Cc: et efface sa valeur courante.
	 */
	public function setCc($email, $name='') {
		unset($this->_headers['Cc']) ;
		$this->addCc($email, $name) ;
		return $this ;
	}

	/**
	 * Défini le Bcc: et efface sa valeur courante.
	 */
	public function setBcc($email, $name='') {
		unset($this->_headers['Bcc']) ;
		$this->addBcc($email, $name) ;
		return $this ;
	}

	

}
