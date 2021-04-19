<?php 

interface ExportInterface {

  //  *** fichier à enregistrer sur le disque  
  static function attr_nom_fichier($object);
  //  *** entête d'export, différente de l'entête spécifique à un format 
  static function entete($object);

}