<?php 
	error_reporting(0); // hide PHP lib warnings
	ini_set('display_errors', 0);

	// Lancement de la session par défaut
	$_COOKIE['PHPSESSID'] = "a1b2c3d4";
	session_start();


	/* *********************************************
	 *
	 * CONFIGURATION DU COMPTE
	 *
	 * ********************************************* */

	 require 'conf.inc';

	 if (
		!isset($config) ||
		empty($config['user_token']) ||
		empty($config['user_secret']) ||
		empty($config['screen_name'])
	) {echo "Votre installation de CLIver est mal configurée. Relancez l'installation ou éditez-vous même le fichier 'conf.inc' pour résoudre le problème"; die;}
        
        
        
        /* *********************************************
         * 
         * /!\ NE RIEN TOUCHER EN DESSOUS
         * 
         * ********************************************* */        


        require 'classes/tmhOAuth.php';
        require 'classes/tmhUtilities.php';
        require 'classes/ArgvParser.php';
	require 'classes/BitLy.php';

	require 'actions.php';
        
        
        /*
         *  On créé les instances pour l'auth twitter et le parser
         */

	global $tmhOAuth, $argvParser;

        $tmhOAuth = new tmhOAuth(array(
          'consumer_key'    => 'NET5CPnOaADB2jXJRlHGKQ',
          'consumer_secret' => 'i32KdbLPaT47pfFR9ZPaUoNi84w6PlLdRCCTWKxLR0',
          'user_token'      => $config['user_token'],
          'user_secret'     => $config['user_secret'],
        ));
        $argvParser = ArgvParser::getInstance();

        try {
		// Appel de l'action
		global $actions;

		// Le premier argument est toujours la commande à exécuter
		$actions[array_keys($argvParser->getArgs())[0]]["callback"]();
        } catch (Exception $e) {
                var_dump($e);
        }
