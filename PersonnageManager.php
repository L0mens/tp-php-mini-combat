<?php

class PersonnagesManager
{
    private $_db ; // Instance de PDO
    public function __construct ( $db )
    {
    $this->setDb ( $db ) ;
    }

    public function add( Personnage $perso )
    {
     // Pré paration de la requête d'insertion .
     $q = $this->_db->prepare('INSERT INTO personnages SET nom = :nom') ;

     // Assignation des valeurs pour le nom du personnage .
     $q->bindValue(':nom', $perso->getNom()) ;

     // Exécution de la requête.
        $q->execute();

    // Hydratation du personnage pass é en param ètre avec assignation de son identifiant et des dégâts initiaux (=0).
     $perso->hydrate(array(
     'id' => $this ->_db->lastInsertId(),
     'degats' => 0,)
     );
    }

    public function count()
    {
     // Exécute une requête COUNT() et retourne le nombre de résultats retourn é.
     return $this->_db->query('SELECT COUNT(*) FROM personnages');
    }

    public function delete( Personnage $perso )
    {
     // Exécute une requête de type DELETE .
        $this ->_db->exec ('DELETE FROM personnages WHERE id = '.$perso ->getId()) ;
    }

    public function exists (int|string $info ) : bool
    {
     // Si le param ètre est un entier , c 'est qu 'on a fourni unidentifiant .
     if ( is_int ( $info ))
     // On exécute alors une requ ête COUNT () avec une clauseWHERE , et on retourne un boolean .
        return ( bool ) $this->_db-> query ( 'SELECT COUNT(*) FROM personnages WHERE id = '. $info )->fetchColumn();
     // Sinon c'est qu 'on a pass é un nom.
     // Exécution d 'une requ ête COUNT () avec une clause WHERE ,et retourne un boolean .
     $q = $this -> _db -> prepare ( 'SELECT COUNT(*) FROM personnages WHERE nom = :nom ') ;
     $q ->execute( array ( ':nom' => $info ) ) ;
     return ( bool ) $q->fetchColumn() ;

    }

    public function get ( $info )
    {
     // Si le paramètre est un entier , on veut récupérer le personnage avec son identifiant .
     if ( is_int ( $info ) )
     {
         // Exécute une requ ête de type SELECT avec une clauseWHERE , et retourne un objet Personnage
        $q = $this -> _db -> query ( 'SELECT id , nom , degats FROM personnages WHERE id = '. $info ) ;
        $donnees = $q -> fetch ( PDO::FETCH_ASSOC ) ;
        return new Personnage ( $donnees ) ;
     }
    // Sinon, on veut récupérer le personnage avec son nom.
     else
    {
        // Exécute une requ ête de type SELECT avec une clause WHERE, et retourne un objet Personnage .
        $q = $this -> _db -> prepare ( 'SELECT id , nom , degats FROM personnages WHERE nom = :nom ') ;
        $q -> execute ( array ( ':nom' => $info ) ) ;
        return new Personnage ( $q -> fetch ( PDO::FETCH_ASSOC ) ) ;
    }
    }

    public function getListExcludingName ( $nom )
    {
     $persos = array () ;
     // Retourne la liste des personnages dont le nom n 'est pas $nom .
     // Le résultat sera un tableau d'instances de Personnage .
     $q = $this ->_db->prepare( 'SELECT id , nom , degats FROM personnages WHERE nom <> :nom ORDER BY nom ') ;
     $q -> execute ( array ( ':nom' => $nom )) ;
     while ( $donnees = $q -> fetch ( PDO::FETCH_ASSOC )){
         $persos[] = new Personnage ( $donnees ) ;
     }
     return $persos;
    }

    public function update ( Personnage $perso )
    {
     // Prépare une requête de type UPDATE . SECONDE ÉTAPE : STOCKAGE EN BASE DE DONNÉES
     $q = $this->_db->prepare( 'UPDATE personnages SET degats = :degats WHERE id = :id ');

     // Assignation des valeurs à la requête.
     $q -> bindValue ( ':degats', $perso->getDegats(), PDO::PARAM_INT) ;
     $q->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

     //Exé cution de la requ ête.
     $q->execute();
    }

    public function setDb ( PDO $db )
    {
         $this->_db = $db ;
    }
}