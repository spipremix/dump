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
include_spip('base/dump');
include_spip('inc/dump');

/**
 * Charger #FORMULAIRE_SAUVEGARDER
 * @return array
 */
function formulaires_sauvegarder_charger_dist(){	
	$dir_dump = dump_repertoire();

	// ici on liste tout, les tables exclue sont simplement non cochees
	$tables = dump_lister_toutes_tables();
	$exclude = lister_tables_noexport();

	$valeurs = array(
		'_dir_dump'=>joli_repertoire($dir_dump),
		'_dir_img'=>joli_repertoire(_DIR_IMG),
		'_spipnet' => $GLOBALS['home_server'] . '/' .  $GLOBALS['spip_lang'] . '_article1489.html',
		'nom_sauvegarde' => dump_nom_fichier($dir_dump),
		'tout_sauvegarder' => (_request('nom_sauvegarde') AND !_request('tout_sauvegarder'))?'':'oui',
		'_tables' => "<ol class='spip'><li class='choix'>\n" . join("</li>\n<li class='choix'>",
		  dump_saisie_tables('tables', $tables, $exclude, _request('nom_sauvegarde')?(_request('tables')?_request('tables'):array()):null)
			) . "</li></ol>\n",
	);

	return $valeurs;
}

/**
 * Verifier
 * @return <type>
 */
function formulaires_sauvegarder_verifier_dist() {
	$erreurs = array();
	if (!$nom = _request('nom_sauvegarde'))
		$erreurs['nom_sauvegarde'] = _T('info_obligatoire');
	elseif (!preg_match(',^[\w_][\w_.]*$,', $nom)
		OR basename($nom)!==$nom)
		$erreurs['nom_sauvegarde'] = _T('dump:erreur_nom_fichier');

	return $erreurs;
}

/**
 * Traiter
 */
function formulaires_sauvegarder_traiter_dist() {
	$status_file = base_dump_meta_name(0);
	$dir_dump = dump_repertoire();
	$archive = $dir_dump . basename(_request('nom_sauvegarde'),".sqlite");

	if (_request('tout_sauvegarder')) {
		// ici on prend toutes les tables sauf celles exclues par defaut
		// (tables de cache en pratique)
		$exclude = lister_tables_noexport();
		$tables = dump_lister_toutes_tables('',$exclude);
	}
	else
		$tables = _request('tables');

	include_spip('inc/dump');
	$res = dump_init($status_file, $archive, $tables);

	if ($res===true) {
		// on lance l'action sauvegarder qui va realiser la sauvegarde
		// et finira par une redirection vers la page sauvegarde_fin
		include_spip('inc/actions');
		$redirect = generer_action_auteur('sauvegarder', $status_file);
		return array('message_ok'=>'ok','redirect'=>$redirect);
	}
	else
		return array('message_erreur'=>$res);
}

/**
 * Fabrique la liste a cocher des tables presentes a sauvegarder
 *
 * @param string $name
 * @param bool $check
 * @return string
 */
function dump_saisie_tables($name, $tables, $exclude, $post=null) {
	foreach ($tables as $k => $t) {
		// par defaut tout est coche sauf les tables dans $exclude
		if (is_null($post))
			$check = (in_array($t,$exclude)?false:true);
		// mais si on a poste une selection, la reprendre
		else
			$check = isset($post[$t]);

		$res[$k] = "<input type='checkbox' value='$t' name='$name"
			. "[]' id='$name$k'"
			. ($check ? " checked='checked'" : '')
			. "/>\n"
			. "<label for='$name$k'>".$t."</label>"
			. " ("
			. sinon(singulier_ou_pluriel(sql_countsel($t), 'dump:une_donnee', 'dump:nb_donnees'),_T('dump:aucune_donnee'))
	  		. ")";
	}
	return $res;
}
?>