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

/**
 * Charger #FORMULAIRE_SAUVEGARDER
 * @return array
 */
function formulaires_sauvegarder_charger_dist(){
	$repertoire = _DIR_DUMP;
	if (!@file_exists($repertoire)
		AND !$repertoire = sous_repertoire(_DIR_DUMP,'',false,true)
	) {
		$repertoire = preg_replace(','._DIR_TMP.',', '', _DIR_DUMP);
		$repertoire = sous_repertoire(_DIR_TMP, $repertoire);
	}
	
	$dir_dump = $repertoire;
	list($check,) = base_liste_table_for_dump(lister_tables_noexport());

	$valeurs = array(
		'_dir_dump'=>joli_repertoire($dir_dump),
		'_dir_img'=>joli_repertoire(_DIR_IMG),
		'_spipnet' => $GLOBALS['home_server'] . '/' .  $GLOBALS['spip_lang'] . '_article1489.html',
		'nom_sauvegarde' => nom_fichier_dump($dir_dump),
		'_tables' => "<ol class='spip'><li class='choix'>\n" . join("</li>\n<li class='choix'>",
				controle_tables_en_base('export', $check)
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
	elseif (!preg_match(',^[\w_][\w_.]*$,', $nom))
		$erreurs['nom_sauvegarde'] = _L('format de nom incorrect');

	return $erreurs;
}

/**
 * Traiter
 */
function formulaires_sauvegarder_traiter_dist() {
	$export = charger_fonction('export_all','exec');
	$export();
	// on ne revient pas ici !
}

/**
 * Nom du fichier de sauvegarde
 *
 * @param string $dir
 * @param string $extension
 * @return string
 */
function nom_fichier_dump($dir,$extension='sqlite'){
	$site = isset($GLOBALS['meta']['nom_site'])
	  ? preg_replace(array(",\W,is",",_(?=_),",",_$,"),array("_","",""), couper(translitteration(trim($GLOBALS['meta']['nom_site'])),30,""))
	  : 'spip';

	$site .= '_' . date('Ymd');

	$nom = $site;
	$cpt=0;
	while (file_exists($dir. $nom . ".$extension")) {
		$nom = $site . sprintf('_%03d', ++$cpt);
	}
	return $nom;
}

/**
 * Fabrique la liste a cocher des tables presentes a sauvegarder
 *
 * @param string $name
 * @param bool $check
 * @return string
 */
function controle_tables_en_base($name, $check)
{
	$p = '/^' . $GLOBALS['table_prefix'] . '/';
	$res = $check;
	foreach(sql_alltable() as $t) {
		$t = preg_replace($p, 'spip', $t);
		if (!in_array($t, $check)) $res[]= $t;
	}
	sort($res);

	foreach ($res as $k => $t) {

		$res[$k] = "<input type='checkbox' value='$t' name='$name"
			. "[]' id='$name$k'"
			. (in_array($t, $check) ? " checked='checked'" : '')
			. "/>\n"
			. "<label for='$name$k'>".$t."</label>"
			. " ("
			.  sql_countsel($t)
	  		. ")";
	}
	return $res;
}
?>