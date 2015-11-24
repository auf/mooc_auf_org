<?php
setlocale(LC_ALL, 'fr_FR.UTF-8') ;

//define("DIR_PJ", "/srv/mooc.auf.org/pj/") ;
define("DIR_PJ", "/srv/AUF/MOOC/mooc/pj/") ;

define("URL_DOMAINE_PUBLIC", "foad-mooc.auf.org") ;
define("URL_DOMAINE", "mooc.refer.org") ;

// Contact et expéditeur des messages
define("NOM_CONTACT", "Agence universitaire de la Francophonie") ;
define("EMAIL_CONTACT", "mooc-clom@auf.org") ;
define("EMAIL_FROMNAME", NOM_CONTACT) ;
define("EMAIL_REPLYTO", EMAIL_CONTACT) ;
define("EMAIL_FROM", "automate@refer.org") ;
define("EMAIL_SENDER", "mooc-clom@refer.org") ;

// Pour les deux messageries (inscrits et CNF)
define("EMAIL_FROM_TOUJOURS", TRUE) ;
define("EMAIL_SENDER_TOUJOURS", TRUE) ;

define("USER_00", "Administrateur") ;
define("EMAIL_00", EMAIL_CONTACT) ;
define("USER_01", "Gestionnaire AUF") ;
define("EMAIL_01", EMAIL_CONTACT) ;
define("USER_02", "Gestionnaire AUF dans un CNF") ;
define("EMAIL_02", EMAIL_CONTACT) ;
define("USER_03", "Visiteur") ;
define("EMAIL_03", "") ;


// Pour la maintenance
define("SITE_EN_LECTURE_SEULE", FALSE) ;
// Message affiché auc candidats et aux gestionnaires et reponsables
define("EN_MAINTENANCE", "Le site est actuellement en maintenance.") ;
define("EN_MAINTENANCE_INFO", "Il demeure consultable, mais toute tentative de mise à jour des données sera un échec.<br />
(Échec avec ou sans message d'erreur technique.)") ;

// Ecran avant formulaire. Son contenu est défini dans inc_inscription.php
define("FLAG_SPLASH", FALSE) ;

// Libellés des liens de l'interface
define("LIEN_IMPUTER", "&nbsp;<strong>&rArr;</strong>&nbsp;&nbsp;Imputer") ;
define("LIEN_IMPUTATION", "Imputation") ;
define("LIEN_DIPLOMER", "&nbsp;<strong>&rArr;</strong>&nbsp;&nbsp;Diplômer") ;
define("LIEN_ANCIEN", "Ancien") ;
define("LIEN_CANDIDATURE", "Candidature") ;
define("LIEN_DOSSIER", "Voir dossier") ;
// Libellés des pictogrammes (en texte) de l'interface
define("LABEL_DIPLOME", "Diplôme") ;
define("LABEL_INSCRIT", "Inscrit") ;

define("MESSAGE_AUTOMATIQUE", "Ce message a été envoyé automatiquement par un robot, merci de ne pas y répondre.") ;

// Filtres
define("LABEL_REINITIALISER", "Réinitialiser") ;
define("LABEL_ACTUALISER", "Actualiser") ;
?>
