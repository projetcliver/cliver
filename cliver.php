<?php 
	error_reporting(0); // hide PHP lib warnings
	ini_set('display_errors', 0);

	$_COOKIE['PHPSESSID'] = "a1b2c3d4";
	session_start();


        /* *********************************************
         * 
         * /!\ CONFIGURATION DE VOTRE COMPTE OBLIGATOIRE
         * 
         * ********************************************* */
        $config = array(
            "user_token" => '94638788-FXenjdbnXyxTt1mwUdp0KQDuTatfSElbIeXMFifnM', // VOTRE_USER_TOKEN
            "user_secret" => '580SMaUExeXyi69bM7p2aAmYyaQcjjDaVtrIEKL1sFk',        // VOTRE_USER_SECRET
            "screen_name" => 'KrisJNT'                                            // VOTRE_SCREEN_NAME
        );
        
        
        
        
        
        
        
        
        /* *********************************************
         * 
         * /!\ NE RIEN TOUCHER EN DESSOUS
         * 
         * ********************************************* */        


        require 'classes/tmhOAuth.php';
        require 'classes/tmhUtilities.php';
        require 'classes/ArgvParser.php';
        
        
        /*
         *  On créé les instances pour l'auth twitter et le parser
         */
        $tmhOAuth = new tmhOAuth(array(
          'consumer_key'    => 'NET5CPnOaADB2jXJRlHGKQ',
          'consumer_secret' => 'i32KdbLPaT47pfFR9ZPaUoNi84w6PlLdRCCTWKxLR0',
          'user_token'      => $config['user_token'],
          'user_secret'     => $config['user_secret'],
        ));
        $argvParser = ArgvParser::getInstance();

        try {
            
            
            
            
                /*
                * On envoie un nouveau tweet
                */
                if($argvParser->isExistOption('send')){
                    $msg = $argvParser->getOption('send');
		    var_dump($msg);die;
                    $code = $tmhOAuth->request('POST', $tmhOAuth->url('1/statuses/update'), array(
                        'status' => $msg
                    ));
                    if($code == 200){
                        echo 'Votre tweet a bien été envoyé !
';
                    }else{
                        tmhUtilities::pr($tmhOAuth->response['response']);
                    }
                }
                
                
                
                
                /*
                * Faire une recherche
                */
                if($argvParser->isExistOption('search')){
                    $search = $argvParser->getOption('search');
                    $code = $tmhOAuth->request('GET', $tmhOAuth->url('1/users/search'), array(
                        'q' => $search
                    ));
                    if($code == 200){
                        $resp = json_decode($tmhOAuth->response['response']);
                        echo 'Liste des comptes trouvés :
';
                        foreach($resp as $uneRep){
                            echo '[@'.$uneRep->screen_name.'] '.$uneRep->name.'
';
                        }
                    }else{
                        echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
                        tmhUtilities::pr($tmhOAuth);
                    }
                }
                                            
                            
                
                
                /*
                * Affiche la version de CLIver
                */
                $arguments = $argvParser->getArguments();
                if(in_array('version', $arguments)){
                    echo 'Version de CLIver : 1.0
';
                }
                
                
                
                
                /*
                * Affiche l'aide de CLIver
                */
                if(in_array('help', $arguments)){
                    echo '
    /!\ Pour utiliser le client, pensez à renseigner votre user_token et votre user_secret dans le fichier cliver.php !
    Pour le obtenir, pour le moment il faut s\'identifier via l\'url : http://alexandre-godet.com/dev/CLIver/auth.php
    Aide :
    Pour le moment la version client permet de :
        - Mettre à jour votre status (envoyer un tweet) : $ cliver --send="Votre message ici"
        - Afficher la version du projet CLIver : $ cliver version
        - Afficher vos infos : $ cliver me
        - Afficher vos derniers tweets : $ cliver readown
	- C\'est tout, pour le moment...
';
                }

                
                
                
                /*
                * Affiche les infos du user connecté
                */
                if(in_array('me', $arguments)){
                    $code = $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
                    if ($code == 200) {
                        $resp = json_decode($tmhOAuth->response['response']);
                        echo 'Vos informations : 
    Votre screen name : @'.$resp->screen_name.'
    Votre nom : '.$resp->name.'
    Nb abonnements  : '.$resp->friends_count.'
    Nb followers  : '.$resp->followers_count.'
    Nb tweets : '.$resp->statuses_count.'
    Langue : '.$resp->lang.'        
    Inscrit depuis le : '.$resp->created_at.'
';
                    } else {
                        outputError($tmhOAuth);
                    }
                }
                
                
                
                
                /*
                * Affiche les derniers tweets de notre compte
                */
                if(in_array('readown', $arguments)){
                    $code = $tmhOAuth->request('GET', $tmhOAuth->url('1/statuses/user_timeline'), array(
                        'screen_name' => $config['screen_name']
                    ));
                    if($code == 200){
                        $resp = json_decode($tmhOAuth->response['response']);
                        echo 'Vos '.count($resp).' derniers tweets : 
    ';                        
                        foreach($resp as $unTweet){
                            echo $unTweet->text.'
    ';
                        }
                        echo '
';
                    }else{
                        echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
                        tmhUtilities::pr($tmhOAuth);
                    }
                }
                

        } catch (Exception $e) {
                var_dump($e);
        }
