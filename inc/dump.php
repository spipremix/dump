<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Repertoire de sauvegarde
 *
 * @return string
 */
function dump_repertoire() {
	$repertoire = _DIR_DUMP;
	if (!@file_exists($repertoire)
		AND !$repertoire = sous_repertoire(_DIR_DUMP,'',false,true)
	) {
		$repertoire = preg_replace(','._DIR_TMP.',', '', _DIR_DUMP);
		$repertoire = sous_repertoire(_DIR_TMP, $repertoire);
	}
	return $repertoire;
}


/**
 * Nom du fichier de sauvegarde
 * la fourniture de l'extension permet de verifier que le nom n'existe pas deja
 *
 * @param string $dir
 * @param string $extension
 * @return string
 */
function dump_nom_fichier($dir,$extension='sqlite'){
	include_spip('inc/texte');
	$site = isset($GLOBALS['meta']['nom_site'])
	  ? preg_replace(array(",\W,is",",_(?=_),",",_$,"),array("_","",""), couper(translitteration(trim($GLOBALS['meta']['nom_site'])),30,""))
	  : 'spip';

	$site .= '_' . date('Ymd');

	$nom = $site;
	$cpt=0;
	while (file_exists($dir. $nom . ".$extension")) {
		$nom = $site . sprintf('_%03d', ++$cpt);
	}
	return $nom.".$extension";
}

/**
 * Determine le type de serveur de sauvegarde
 * sqlite2 ou sqlite3
 * 
 * @return string
 */
function dump_type_serveur() {

	// chercher si sqlite2 ou 3 est disponible
	include_spip('req/sqlite3');
	if (spip_versions_sqlite3())
		return 'sqlite3';

	include_spip('req/sqlite2');
	if (spip_versions_sqlite2())
		return 'sqlite2';

	return '';
}

/**
 * Conteneur pour les arguments de la connexion
 * si on passe $args, les arguments de la connexion sont memorises
 * renvoie toujours les derniers arguments memorises
 *
 * @staticvar array $connect_args
 * @param array $connect
 * @return array
 */
function dump_serveur($args=null) {
	static $connect_args = null;
	if ($args)
		$connect_args = $args;

	return $connect_args;
}

/**
 * Lister toutes les tables a copier depuis un serveur
 * en excluant eventuellement une liste fournie
 * 
 * @param string $serveur
 * @param array $exclude
 * @return array 
 */
function dump_lister_toutes_tables($serveur='', $exclude = array()) {
	list($tables,) = base_liste_table_for_dump($exclude);

	$connexion = $GLOBALS['connexions'][$serveur ? $serveur : 0];
	$prefixe = $connexion['prefixe'];

	$p = '/^' . $prefixe . '/';
	$res = $tables;
	foreach(sql_alltable(null,$serveur) as $t) {
		$t = preg_replace($p, 'spip', $t);
		if (!in_array($t, $tables) AND !in_array($t, $exclude)) $res[]= $t;
	}
	sort($res);
	return $res;
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
			$check = in_array($t,$post);

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

function dump_connect_args($archive) {
	if (!$type_serveur = dump_type_serveur())
		return null;
	return array(dirname($archive), '', '', '', basename($archive,".sqlite"), $type_serveur, 'spip');
}

/**
 * Initialiser un dump
 * @param string $status_file
 * @param string $archive
 * @param array $tables
 * @param array $where
 * @return bool/string
 */
function dump_init($status_file, $archive, $tables=null, $where=array()){
	$status_file = _DIR_TMP.basename($status_file).".txt";

	if (lire_fichier($status_file, $status)
		AND $status = unserialize($status)
		AND $status['etape']!=='fini'
		AND filemtime($status_file)>=time()-120) // si le fichier status est trop vieux c'est un abandon
		return _T('dump:erreur_sauvegarde_deja_en_cours');

	if (!$type_serveur = dump_type_serveur())
		return _T('dump:erreur_sqlite_indisponible');

	if (!$tables)
		list($tables,) = base_liste_table_for_dump(lister_tables_noexport());
	$status = array('tables'=>$tables,'where'=>$where,'archive'=>$archive);

	$status['connect'] = dump_connect_args($archive);
	dump_serveur($status['connect']);
	if (!spip_connect('dump'))
		return _T('dump:erreur_creation_base_sqlite');

	// la constante sert a verifier qu'on utilise bien le connect/dump du plugin,
	// et pas une base externe homonyme
	if (!defined('_DUMP_SERVEUR_OK'))
		return _T('erreur_connect_dump', array('dump' => 'dump'));

	$status['etape'] = 'init';

	if (!ecrire_fichier($status_file, serialize($status)))
		return _T('dump:avis_probleme_ecriture_fichier',array('fichier'=>$status_file));

	return true;
}

/**
 * Ecrire le js pour relancer la procedure de dump
 * @param string $redirect
 * @return string
 */
function dump_relance($redirect){
	// si Javascript est dispo, anticiper le Time-out
	return "<script language=\"JavaScript\" type=\"text/javascript\">window.setTimeout('location.href=\"$redirect\";',300);</script>\n";
}


/**
 * Marquer la procedure de dump comme finie
 * @param string $status_file
 * @return <type>
 */
function dump_end($status_file){
	$status_file = _DIR_TMP.basename($status_file).".txt";
	if (!lire_fichier($status_file, $status)
		OR !$status = unserialize($status))
		return;
	$status['etape'] = 'fini';
	ecrire_fichier($status_file, serialize($status));
}

/**
 * Lister les fichiers de sauvegarde existant dans un repertoire
 * trie par nom, date ou taille
 * 
 * @param string $dir
 * @param string $tri
 * @param string $extension
 * @param int $limit
 * @return array
 */
function dump_lister_sauvegardes($dir,$tri='nom',$extension="sqlite",$limit = 100) {
	$liste_dump = preg_files($dir,'\.'.$extension.'$',$limit,false);

	$n = strlen($dir);
	$tn = $tl = $tt = $td = array();
	foreach($liste_dump as $fichier){
		$d = filemtime($fichier);
		$t = filesize($fichier);
		$fichier = substr($fichier, $n);
		$tl[]= array('fichier'=>$fichier,'taille'=>$t,'date'=>$d);
		$td[] = $d;
		$tt[] = $t;
		$tn[] = $fichier;
	}
	if ($tri == 'taille')
		array_multisort($tt, SORT_ASC, $tl);
	elseif ($tri == 'date')
		array_multisort($td, SORT_ASC, $tl);
	else
		array_multisort($tn, SORT_ASC, $tl);
	return $tl;
}

function dump_afficher_liste_sauvegardes($liste,$caption,$name,$selected,$url,$tri='nom',$id="sauvegardes"){
	if (!count($liste))
		return '';

	$fichier_defaut = $f ? basename($f) : str_replace(array("@stamp@","@nom_site@"),array("",""),_SPIP_DUMP);

	$self = self();
	$class = 'row_'.alterner($i+1, 'even', 'odd');

	$table = "<div class='liste-objets dump' id='$id'><table class='spip liste'><thead>"
	  . ($caption?"<caption><strong class='caption'>$caption</strong></caption>":"")
		. "<tr class='first_row'>"
		. ($name?'<th></th>':'')
	  . '<th>'
		. lien_ou_expose(ancre_url(parametre_url($url,'tri','nom'),"#$id"), _T('info_nom'), $tri=="nom")
	  . '</th><th>'
		. lien_ou_expose(ancre_url(parametre_url($url,'tri','taille'),"#$id"), _T('dump:info_taille'), $tri=="taille")
	 	. '</th><th>'
		. lien_ou_expose(ancre_url(parametre_url($url,'tri','date'),"#$id"), _T('public:date'), $tri=="date")
		. '</th></tr>'
		. '</thead>'
		. '<tbody>';

	$i=0;
	foreach($liste as $f) {
		$i++;
		$ligne = "<tr class='".alterner($i,'row_odd','row_even')."'>";
		if ($name) {
			$ligne .= "<td><input type='radio' name='$name' value='"
			  . $f['fichier']
			  . "' id='dump_$i' "
			  . ($f['fichier']==$selected?"checked='checked' ":"")
			  . "/></td>";
		}
		$ligne .= "<td class='principale'>\n<label for='dump_$i'>"
		  . str_replace('/', ' / ', $f['fichier'])
		  . "</label></td>";
		$ligne .= "<td style='text-align: right'>"
		  . taille_en_octets($f['taille'])
		  . '</td>';
		$ligne .= '<td>'
		  . affdate_heure(date('Y-m-d H:i:s',$f['date']))
		  . '</td>';
		$ligne .= '</tr>';
		$table .= $ligne;
	}

	$table .="</tbody></table></div>";

	return $table;
}


?>