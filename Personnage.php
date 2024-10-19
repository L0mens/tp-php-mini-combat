<?php

class Personnage
{
    private int $_id;

    private string $_nom;
    private int $_degats;

    const CEST_MOI = 1 ;
    const PERSONNAGE_TUE = 2 ;
    const PERSONNAGE_FRAPPE = 3 ;

    const PV_MAX = 100;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    public function frapper ( Personnage $perso ) : int
    {
    /* Avant tout : vérifier qu'on ne se frappe pas soi-même.
    Si c'est le cas, on stoppe tout en renvoyant une valeur signifiant que le personnage ciblé est le personnage qui
    attaque.*/
        if($perso->getId() == $this->_id)
            return self::CEST_MOI;
     /*On indique au personnage frappé qu'il doit recevoir des dégâts. */
        return $perso->recevoirDegats();
    }

    /**
     * @throws \Random\RandomException
     */
    public function recevoirDegats() : int
    {
        /* On augmente de [3-10] les dégâts. */
        $damage = random_int(3, 10);
        $this->_degats =+ $damage;
        /*Si on a 100 de dégâts ou plus, la méthode renverra une
        valeur signifiant que le personnage a été tué.*/
        if($this->_degats > self::PV_MAX)
            return self::PERSONNAGE_TUE;
        /*
        Sinon, elle renverra une valeur signifiant que le personnage a bien été frappé.
        */
        else
            return self::PERSONNAGE_FRAPPE;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->_id = $id;
    }

    /**
     * @return string
     */
    public function getNom(): string
    {
        return $this->_nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->_nom = $nom;
    }

    /**
     * @return int
     */
    public function getDegats(): int
    {
        return $this->_degats;
    }

    /**
     * @param int $degats
     */
    public function setDegats(int $degats): void
    {
        $this->_degats = $degats;
    }

    public function hydrate( array $donnees )
    {
        foreach ($donnees as $key=>$value )
        {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method))
                $this -> $method ($value);
        }

    }


}