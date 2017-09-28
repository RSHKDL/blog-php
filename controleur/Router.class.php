<?php

/**
 * @author Olivier ROGER <roger.olivier[ at ]gmail.com>
 * @version $Revision: 231 $
 * @license Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Router permettant la mise en place du pattern MVC
 * Permet un routage simple ou à base de règle de routage.
 * Supporte également le routage multilingue
 * 
 * @version 1.2.2
 * @copyright  2007-2012 Olivier ROGER <roger.olivier[ at ]gmail.com>
 *
 * <code>
 * $router = Router::getInstance();
 * $router->setPath(ROOT_PATH.'includes/controllers/'); // Chemin vers les controlleurs
 * $router->addRule('test/regles/:id/hello',array('controller'=>'index','action'=>'withRule'));
 * </code>
 */
class Router
{

    /**
     * Instance du router
     * @static
     * @var Router
     */
    static private $instance;

    /**
     * Controller à  utiliser. Par defaut index
     * @var string
     */
    private $controller;

    /**
     * Action du controller. Par défaut index
     * @var string
     */
    private $action;

    /**
     * Tableau des paramètres
     * @var array
     */
    private $params;

    /**
     * Liste des règles de routage
     * @var array
     */
    private $rules;

    /**
     * Chemin vers le dossier contenant les controllers
     * @var string
     */
    private $path;

    /**
     * Fichier à  inclure
     * @var string
     */
    private $file;

    /**
     * Controller par defaut (index)
     * @var string
     */
    private $defaultController;

    /**
     * Action par defaut (index)
     * @var string
     */
    private $defaultAction;

    /**
     * Controller à  appeler en cas d'erreur. Par defaut error
     * @var string
     */
    private $errorController;

    /**
     * Action à  appeler en cas d'erreur. par defaut index
     * @var string
     */
    private $errorAction;
	
	/**
	 * Le router gère t'il les url du type site.com/fr/controller/action
	 * @var boolean 
	 */
	private $isMultiLangue	= false;
	
	/**
	 * Code langue choisie
	 * @var string 
	 */
	private $codeLangue		= '';
	
    /**
     *Liste des traductions pour les url multilingues
     * @var array 
     */
	private $tradController;

    /**
     * Singleton de la classe
     * @return Router
     */
    public function getInstance()
    {
        if (!isset(self::$instance))
            self::$instance = new Router();
        return self::$instance;
    }

    /**
     * Charge le controller demandé.
     * Prend en compte les règles de routages si nécessaire
     */
    public function load()
    {
        $url        = $_SERVER['REQUEST_URI'];
        $script     = $_SERVER['SCRIPT_NAME'];
        //Permet de nettoyer l'url des éventuels sous dossier
        $tabUrl     = $this->formatUrl($url, $script);
        $isCustom   = false;
        
        //Supression des éventuelles parties vides de l'url
        $this->clear_empty_value($tabUrl);

        if (!empty($this->rules))
        {
            foreach ($this->rules as $key => $data) {
                $params = $this->matchRules($key, $tabUrl);
                if ($params)
                {
                    $this->controller   = $data['controller'];
                    $this->action       = $data['action'];
                    $this->params       = $params;
                    $isCustom           = true;
                    break;
                }
            }
        }

        if (!$isCustom)
            $this->getRoute($tabUrl);

        $this->controller   = (!empty($this->controller)) ? $this->controller : $this->defaultController;
        $this->action       = (!empty($this->action)) ? $this->action : $this->defaultAction;
        $ctrlPath           = str_replace('_', DIRECTORY_SEPARATOR, $this->controller); // Gestion des sous dossiers dans les controllers
        $this->file         = realpath($this->path) . DIRECTORY_SEPARATOR . $ctrlPath . '.php';
        
		//is_file bien plus rapide que file_exists
        if (!is_file($this->file))
        {
            header("Status: 404 Not Found");
            $this->controller   = $this->errorController;
            $this->action       = $this->errorAction;
            $this->file         = $this->path . $this->controller . '.php';
        }

        //Inclusion du controller
        include $this->file;

        $class      = $this->controller . 'Controller';
		if(!empty($this->codeLangue))
			$controller = new $class($this->getParameters(),$this->codeLangue);
		else
			$controller = new $class($this->getParameters());

        if (!is_callable(array($controller, $this->action)))
            $action = $this->defaultAction;
        else
            $action = $this->action;

        $controller->$action();
    }

    /**
     * Ajoute une règle de routage.
     *
     * @param string $rule Règles de routage : /bla/:param1/blabla/:param2/blabla
     * @param array $target Cible de la règle : array('controller'=>'index','action'=>'test')
     */
    public function addRule($rule, $target)
    {
        if ($rule[0] != '/')
            $rule = '/' . $rule; //Ajout du slashe de début si absent

            $this->rules[$rule] = $target;
    }

    /**
     * Vérifie si l'url correspond à  une règle de routage
     * @link http://blog.sosedoff.com/2009/07/04/simpe-php-url-routing-controller/
     * @param string $rule
     * @param array $dataItems
     * @return boolean|array
     */
    public function matchRules($rule, $dataItems)
    {
        $ruleItems = explode('/', $rule);
        $this->clear_empty_value($ruleItems);

        if (count($ruleItems) == count($dataItems))
        {
            $result = array();
            foreach ($ruleItems as $rKey => $rValue) {
                if ($rValue[0] == ':')
                {
                    $rValue = substr($rValue, 1); //Supprime les : de la clé
                    $result[$rValue] = $dataItems[$rKey];
                }
                else
                {
                    if ($rValue != $dataItems[$rKey])
                        return false;
                }
            }
            if (!empty($result))
                return $result;

            unset($result);
        }
        return false;
    }

    /**
     * Défini une route simple
     * @param array $url
     */
    private function getRoute($url)
    {
        $items = $url;

        if (!empty($items))
        {
            if($this->isMultiLangue)
				$this->codeLangue = array_shift ($items);
			
			$this->controller   = array_shift($items);
            $this->action       = array_shift($items);
			$size = count($items);
			if($size >= 2)
				for($i=0; $i< $size; $i += 2) {
					$key	= (isset($items[$i])) ? $items[$i] : $i;
					$value	= (isset($items[$i+1])) ? $items[$i+1] : null;
					$this->params[$key] = $value;
				}
			else
				$this->params = $items;
			
			//Permet d'avoir des URL multilingue
			if(!empty($this->tradController))
			{
				if(isset($this->tradController[$this->codeLangue][$this->controller]['controllerName']))
				{
					$controller = $this->tradController[$this->codeLangue][$this->controller]['controllerName'];
					if(!empty($controller))
						$this->controller = $controller;
				}
				
				if(isset($this->tradController[$this->codeLangue][$this->controller]['actionsNames'][$this->action]))
				{
					$action = $this->tradController[$this->codeLangue][$this->controller]['actionsNames'][$this->action];
					if(!empty($action))
						$this->action = $action;
				}
			}
        }
    }

    /**
     * Défini le chemin des controllers
     * @param string $path
     */
    public function setPath($path)
    {
        if (is_dir($path) === false)
        {
            throw new Util_Exception('Controller invalide : ' . $path);
        }

        $this->path = $path;
    }
	
	/**
	 * Défini le router comme pouvant gérer ou non le multinlangue
	 * @param boolean $is 
	 */
	public function setMultiLangue($is)
	{
		$this->isMultiLangue = $is;
	}
	
	/**
	 * Défini un tableau permettant d'avoir des URL multi langue.
	 * Format du tableau : 
	 * 
	 * @param array $trad format : 
	 * $urlTraduction = array(
		'fr'=>array(
			'accueil'=>array(
				'controllerName'	=> 'index',
				'actionsNames'		=> array(
					'presentation'	=> 'index',
					'liste'			=> 'list',
					'recherche'		=> 'search'
				)
			)
		),
		'en'=>array(...));
	 */
	public function setControllerTraduction($trad)
	{
		$this->tradController = $trad;
	}

    /**
     * Défini le controller et l'action par défaut
     * @param string $controller
     * @param string $action
     */
    public function setDefaultControllerAction($controller, $action)
    {
        $this->defaultController    = $controller;
        $this->defaultAction        = $action;
    }

    /**
     * Défini le controller et l'action en cas d'erreur
     * @param string $controler
     * @param string $action
     */
    public function setErrorControllerAction($controller, $action)
    {
        $this->errorController  = $controller;
        $this->errorAction      = $action;
    }

    /**
     * Renvoi les paramètres disponibles
     * @return array
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * Supprime d'un tableau tous les élements vide
     * @param array $array
     */
    private function clear_empty_value(&$array)
    {
        foreach ($array as $key => $value) {
            if (empty($value))
                unset($array[$key]);
        }
        $array = array_values($array); // Réorganise les clÃ©s
    }

    /**
     * Supprime les sous dossier d'une url si nécessaire
     * @param string $url
     * @return string
     */
    private function formatUrl($url, $script)
    {
        $tabUrl     = explode('/', $url);
        $tabScript  = explode('/', $script);
        $size       = count($tabScript);

        for ($i = 0; $i < $size; $i++)
            if ($tabScript[$i] == $tabUrl[$i])
                unset($tabUrl[$i]);

        return array_values($tabUrl);
    }
    
    /**
     * Constructeur
     */
    private function __construct()
    {
        $this->rules = array();
        $this->defaultController    = 'index';
        $this->defaultAction        = 'index';
        $this->errorController      = 'error';
        $this->errorAction          = 'index';
    }

}
