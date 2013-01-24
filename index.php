<?php session_start(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=utf-8">
        <title>Projet CLIver</title>
        <style>
            body{
                padding:0;
                margin:0;
            }
            #header{
                height:190px;
                text-align:center;
                line-height: 130px;
                font-size: 30px;
                color:#fff;
                border-bottom: solid 1px #555;
                background:url('https://si0.twimg.com/images/themes/theme1/bg.png');
            }
            #contenu{
                padding:15px;
            }
            h2{
                width:600px;
                color:#555;
                border-bottom: solid 1px #ccc;
            }
        </style>
    </head>
    <body>
            
        
            <div id="header">
                <h1>CLIver</h1>
            </div>

        
            <div id="contenu">
<?php

                require 'classes/tmhOAuth.php';
                require 'classes/tmhUtilities.php';
                
                
                if(isset($_SESSION['access_token'])){
                    
                    echo '
                    <h2>Vos informations :</h2>
                    <i>Ces informations vous permettent d\'utiliser CLIver en ligne de commande</i><br /><br />
                    <b>Votre user_token :</b> '.$_SESSION['access_token']['oauth_token'].'<br />
                    <b>Votre user_secret :</b> '.$_SESSION['access_token']['oauth_token_secret'].'<br />
                    <b>Votre screen_name :</b> @'.$_SESSION['access_token']['screen_name'].'<br />
                    <b>Votre user_id (ID twitter):</b> '.$_SESSION['access_token']['user_id'].'<br /><br />';
                    
                    echo '
                    <h2>Sources du projet:</h2>
                    <a href="CLIver.zip">Télécharger le fichier zip</a><br /><br />
                    
                    <h2>Session :</h2>
                    <a href="auth.php?wipe=1">Détruire la session</a>';
                    
                }else{
                    header('Location: auth.php?authenticate=1'); 
                }
?>
            </div>
            
            
    </body>
</html>