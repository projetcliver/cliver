<?php session_start();


        /* *********************************************
         * 
         * /!\ CONFIGURATION DE VOTRE COMPTE OBLIGATOIRE
         * 
         * ********************************************* */
        $config = array(
            "user_token" => '961054309-nqoEFc6mYQHvhmHkmOHEmVnpgTGn7sQdJlmCs5RY', // VOTRE_USER_TOKEN
            "user_secret" => 'Qkfi4Fbro9lWHiLKpzVFik30UemsIR7e8iiUtgMrHk',        // VOTRE_USER_SECRET
            "screen_name" => 'QLEXX85'                                            // VOTRE_SCREEN_NAME
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
        - Mettre à jour votre status (envoyer un tweet) : $ php cliver.php --send="Votre message ici"
        - Afficher la version du projet CLIver : $ php cliver.php version
        - Afficher vos infos : $ php cliver.php me
        - Afficher vos derniers tweets : $ php cliver.php readown
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