<?php

global $actions;
$actions = array(
	"send" => array(
		"help" => "Envoie un tweet en votre nom. Utilisation : $ cliver send=\"Votre message\" [replyto=id_du_tweet]",
		"callback" => function() {
			global $tmhOAuth;
			global $argvParser;

			$msg = $argvParser->getOption('send');
			$replyTo = $argvParser->getOption('replyto');

			$matches = [];
			if (preg_match_all("((http:\/\/|https:\/\/)?(www.)?(([a-zA-Z0-9-]){2,}\.){1,4}([a-zA-Z]){2,6}(\/([a-zA-Z-_\/\.0-9#:?=&;,]*)?)?)", $msg, $matches)) {
			    $links=$matches[0];
			    foreach ($links as $l) {
				if (substr($l, 0, 4) != "http") $l = 'http://'.$l;
				$msg = str_replace($l, BitLy::shorten($l), $msg);
			    }
			}

			$args = array();
			$args['status'] = $msg;

			if (isset($replyTo) && !empty($replyTo)) {
				$args['in_reply_to_status_id'] = $replyTo;
				$code = $tmhOAuth->request('GET', $tmhOAuth->url('1/statuses/show/'.$replyTo));

				if ($code == 200) {
					$resp = json_decode($tmhOAuth->response['response']);
					$args['status'] = '@' . $resp->user->screen_name . ' '. $args['status'];
				}
			}


			$code = $tmhOAuth->request('POST', $tmhOAuth->url('1/statuses/update'), $args);

			if ($code == 200){
				echo 'Votre tweet a bien été envoyé !'."\n";
			} else {
				tmhUtilities::pr($tmhOAuth->response['response']);
			}
		}	
	),

	"search" => array(
		"help" => "Effectue une recherche sur Twitter. Utilisation : $ cliver search=\"Votre recherche\"",
		"callback" => function() {
			global $tmhOAuth;
			global $argvParser;

			$search = $argvParser->getOption('search');
			$code = $tmhOAuth->request('GET', $tmhOAuth->url('1/users/search'), array(
			    'q' => $search
			));
			if($code == 200){
			    $resp = json_decode($tmhOAuth->response['response']);
			    echo 'Liste des comptes trouvés :'."\n";
			    foreach($resp as $uneRep){
				echo '[@'.$uneRep->screen_name.'] '.$uneRep->name."\n";
			    }
			}else{
			    echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
			    tmhUtilities::pr($tmhOAuth);
			}
		}
	),

	"userinfo" => array(
		"help" => "Récupère les informations d'un utilisateur. Utilisation : $ cliver userinfo=utilisateur",
		"callback" => function() {
			global $tmhOAuth;
			global $argvParser;

			$username = $argvParser->getOption('userinfo');
			$code = $tmhOAuth->request('GET', $tmhOAuth->url('1/users/show'), array(
				'screen_name' => $username
			));

			if ($code == 200) {
				$resp = json_decode($tmhOAuth->response['response']);

				echo 'Informations sur l\'utilisateur :'."\n";

				echo 'Nom: ('.$resp->screen_name.') '.$resp->name."\n";
				echo 'Inscrit le: '.$resp->created_at."\n";
				echo 'Habite à: '.$resp->location."\n";
				echo 'Suivi par: '.$resp->followers_count.' personne(s)'."\n";
				echo 'Nombre d\'abonnements: '.$resp->friends_count."\n";
				echo 'Nombre de tweets: '.$resp->statuses_count."\n";
				echo 'Description: '.$resp->description."\n";
			} else {
				echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
				tmhUtilities::pr($tmhOAuth);
			}
		}
	),

	"version" => array(
		"help" => "Affiche la version de CLIver",
		"callback" => function() {
			echo 'Version de CLIver : 1.1'."\n";
		}
	),

	"help" => array(
		"help" => "Affiche cette aide",
		"callback" => function() {
			global $actions;

			$c = "
== CLIver ==
Si CLIver vous indique un problème d'installation, c'est probablement parce que vos indentifiants ont mal été renseignés. Vous pouvez relancer le script d\'installation qui vous guidera pour les obtenir.

Utilisation:
";
			foreach ($actions as $cmd => $a) $c.= ' - '.$cmd.': '.$a['help'].($a['callback']==null?" (NYI)":"")."\n";
			$c.= "C'est tout, pour le moment...\n";

			echo $c;
		}
	),

	"me" => array(
		"help" => "Affiche vos informations personnelles",
		"callback" => function() {
			global $tmhOAuth;

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
    Inscrit depuis le : '.$resp->created_at."\n";
			} else {
			    outputError($tmhOAuth);
			}
		}
	),

	"readown" => array(
		"help" => "Affiche vos derniers tweets",
		"callback" => function() {
			global $tmhOAuth;

			$code = $tmhOAuth->request('GET', $tmhOAuth->url('1/statuses/user_timeline'), array(
			    'screen_name' => $config['screen_name']
			));
			if($code == 200){
			    $resp = json_decode($tmhOAuth->response['response']);
			    echo 'Vos '.count($resp).' derniers tweets : '."\n";                        
			    foreach($resp as $unTweet){
				echo $unTweet->text."\n";
			    }
			    echo "\n";
			}else{
			    echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
			    tmhUtilities::pr($tmhOAuth);
			}	
		}
	),

	"retweet" => array(
		"help" => "Retweet un message. Utilisation : $ cliver retweet=id_du_tweet",
		"callback" => function() {
			global $tmhOAuth;
			global $argvParser;

			$idTweet = $argvParser->getOption('retweet');

			$code = $tmhOAuth->request('POST', $tmhOAuth->url('1/statuses/retweet/'.$idTweet.'.json'), array(
				'id' => $idTweet
				));

			if ($code == 200){
				echo 'Votre retweet a bien été envoyé !'."\n";
			} else {
				tmhUtilities::pr($tmhOAuth->response['response']);
			}
		}
	),

	"follow" => array(
		"help" => "Permet de suivre un utilisateur. Utilisation : $ cliver follow=utilisateur",
		"callback" => function() {
			global $tmhOAuth;
			global $argvParser;

			$screen_name = $argvParser->getOption('follow');

			$code = $tmhOAuth->request('POST', $tmhOAuth->url('1/friendships/create'), array(
				'screen_name' => $screen_name
			));

			if ($code == 200){
				echo 'Vous suivez à présent '.$screen_name.' !'."\n";
			} else {
				tmhUtilities::pr($tmhOAuth->response['response']);
			}
		}
	),

	"unfollow" => array(
		"help" => "Permet de ne plus suivre un utilisateur. Utilisation : $ cliver unfollow=utilisateur",
		"callback" => function() {
			global $tmhOAuth;
			global $argvParser;

			$screen_name = $argvParser->getOption('unfollow');

			$code = $tmhOAuth->request('POST', $tmhOAuth->url('1/friendships/destroy'), array(
				'screen_name' => $screen_name
			));

			if ($code == 200){
				echo 'Vous ne suiverez plus '.$screen_name.' !'."\n";
			} else {
				tmhUtilities::pr($tmhOAuth->response['response']);
			}
		}
	)
);
