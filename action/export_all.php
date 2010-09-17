<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 *
 * On arrive ici depuis exec=admin_tech
 * - le premier coup on initialise par exec_export_all_args puis export_all_start
 * - ensuite on enchaine sur inc/export, qui remplit le dump et renvoie ici a chaque timeout
 * - a chaque coup on relance inc/export
 * - lorsque inc/export a fini, il retourne $arg
 * - on l'utilise pour clore le fichier
 * - on renvoie
 *   vers action=export_all pour afficher le resume
 *
 */

include_spip('base/dump');
include_spip('inc/export');

// http://doc.spip.org/@exec_export_all_dist
function action_export_all_dist($arg=null){
	if (!$arg) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$status_file = $arg;
	$redirect = parametre_url(generer_action_auteur('export_all',$status_file),"step",intval(_request('step')+1),'&');

	// lancer export qui va se relancer jusqu'a sa fin
	$export = charger_fonction('export', 'inc');
	utiliser_langue_visiteur();
	// quand on sort de $export avec true c'est qu'on a fini
	if ($export($status_file,$redirect)) {
		export_end($status_file);
		include_spip('inc/headers');
		echo redirige_formulaire(generer_url_ecrire("backup_fin",'status='.$status_file,'',true, true));

	}

}

?>
