<?php

defined('_PHP_CONGES') or die('Restricted access');

$titre = "Clôture d'exercice globale";
$formId = uniqid();
$sql = \includes\SQL::singleton();
$config = new \App\Libraries\Configuration($sql);
$DateReliquats = $config->getDateLimiteReliquats();
$isReliquatsAutorise = $config->isReliquatsAutorise();

$datePickerOpts = [
    'format' => "yyyy",
    'viewMode' => "years",
    'minViewMode' => "years",
];

if (1 == getpost_variable('cloture_globale', 0) && getpost_variable('formId', 0) === $formId) {
    $error = '';
    $anneeFinReliquats = intval(getpost_variable('annee', 0));
    $feries = getpost_variable('feries', 0);
    $typeConges = (\App\ProtoControllers\Conge::getTypesAbsences($sql, "conges") 
                + \App\ProtoControllers\Conge::getTypesAbsences($sql, "conges_exceptionnels"));
    $employes = \App\ProtoControllers\Utilisateur::getDonneesTousUtilisateurs($config);
    $ClotureExercice = \App\ProtoControllers\ClotureExercice;

    if (0 != count($employes)) {
        if ($ClotureExercice::traitementClotureEmploye($employes, $typeConges, $error, $sql, $config)) {
            $ExerciceResult = $ClotureExercice::updateNumExerciceGlobal($sql);
            if($isReliquatsAutorise && $ExerciceResult) {
                $ClotureExercice::updateDateLimiteReliquats($anneeFinReliquats, $error, $sql);
            }
        }
        if(1 == $feries) {
            $ClotureExercice::setJoursFeriesFrance();
        }
    }
}

require_once VIEW_PATH . 'ClotureExercice/ClotureGlobale.php';
