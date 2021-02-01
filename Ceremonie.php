<?php
class fonction_ceremonie {

    /**
     * gestion statique des accès SGBD
     * @var PDO
     */ 
    private static $_pdo;

    /**
     * gestion statique de la requête préparée de selection
     * @var PDOStatement
     */ 
    private static $_pdos_select;

    /**
     * gestion statique de la requête préparée de mise à jour
     *  @var PDOStatement
     */ 
    private static $_pdos_update;

    /**
     * gestion statique de la requête préparée de d'insertion
     * @var PDOStatement
     */ 
    private static $_pdos_insert;

    /**
     * gestion statique de la requête préparée de suppression
     * @var PDOStatement
     */ 
    private static $_pdos_delete;

    /**
     * PreparedStatement associé à un SELECT, calcule le nombre de livres de la table
     * @var PDOStatement;
        */
    private static $_pdos_count;

    /**
     * PreparedStatement associé à un SELECT, récupère tous les livres
     * @var PDOStatement;
        */
     private static $_pdos_selectAll;



    /**
     * Initialisation de la connexion et mémorisation de l'instance PDO dans fonction_prix::$_pdo
     */ 
    public static function initPDO() {
        self::$_pdo = new PDO("pgsql:host=localhost;dbname=util", "util", "utilpass");
        // pour récupérer aussi les exceptions provenant de PDOStatement
        self::$_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * préparation de la requête SELECT * FROM Ceremonie
     * instantiation de self::$_pdos_selectAll
        */
    public static function initPDOS_selectAll() {
        self::$_pdos_selectAll = self::$_pdo->prepare('SELECT * FROM Ceremonie');
    }

     /**
     * méthode statique instanciant fonction_prix::$_pdo_select
     */ 
    public static function initPDOS_select() {
        self::$_pdos_select = self::$_pdo->prepare('SELECT * FROM Ceremonie WHERE id_ceremonie= :numero');
    }

    /**
     * méthode statique instanciant fonction_prix::$_pdo_update
     */ 
    public static function initPDOS_update() {
        self::$_pdos_update =  self::$_pdo->prepare('UPDATE Ceremonie SET nom_ceremonie=:nom, id_organisation=:organisation WHERE id_ceremonie=:numero');
    }

    /**
     * méthode statique instanciant fonction_prix::$_pdo_insert
     */ 
    public static function initPDOS_insert() {
        self::$_pdos_insert = self::$_pdo->prepare('INSERT INTO Ceremonie VALUES(:numero,:nom,:organisation)');
    }

    /**
     * méthode statique instanciant fonction_prix::$_pdo_delete
     */ 
    public static function initPDOS_delete() {
        self::$_pdos_delete = self::$_pdo->prepare('DELETE FROM Ceremonie WHERE id_ceremonie=:numero');
    }

    /**
     * préparation de la requête SELECT COUNT(*) FROM Ceremonie
     * instantiation de self::$_pdos_count
        */
    public static function initPDOS_count() {
        if (!isset(self::$_pdo))
            self::initPDO();
        self::$_pdos_count = self::$_pdo->prepare('SELECT COUNT(*) FROM Ceremonie');
    }


     /**
     * numéro du Ceremonie (identifiant dans la table Ceremonie)
     * @var int
     */ 
    protected $id_ceremonie;

    /**
     * nom du Ceremonie
     * @var string
     */ 
    protected $nom_ceremonie;

    /**
     * dépot légal du Ceremonie
     *   @var string
     */ 
    protected $id_organisation;

    /**
     * attribut interne pour différencier les nouveaux objets des objets créés côté applicatif de ceux issus du SGBD
     * @var bool
     */ 
    private $nouveau = TRUE;

    /**
     * @return $this->id_ceremonie
     */ 
    public function getid_ceremonie() : int {
        return $this->id_ceremonie;
    }

    /**
     * @param $id_ceremonie
     */ 
    public function setid_ceremonie($id_ceremonie): void {
        $this->id_ceremonie=$id_ceremonie;
    }

    /**
     * @return $this->nom_ceremonie
     */ 
    public function getnom_ceremonie() : string {
        return $this->nom_ceremonie;
    }

    /**
     * @param $nom_ceremonie
     */ 
    public function setnom_ceremonie($nom_ceremonie): void {
        $this->nom_ceremonie=$nom_ceremonie;
    }

    /**
     * @return $this->id_organisation
     */ 
    public function getid_organisation() : string {
        return $this->id_organisation;
    }

    /**
     * @param $id_organisation
     */ 
    public function setid_organisation($id_organisation): void {
        $this->id_organisation=$id_organisation;
    }

    /**
     * @return $this->nouveau
     */ 
    public function getNouveau() : bool {
        return $this->nouveau;
    }

    /**
     * @param $nouveau
     */ 
    public function setNouveau($nouveau): void {
        $this->nouveau=$nouveau;
    }

    /**
     * @return un tableau de tous les fonction_prix
     */ 
    public static function getAll(): array {
        try {
            if (!isset(self::$_pdo))
                self::initPDO();
            if (!isset(self::$_pdos_selectAll))
                self::initPDOS_selectAll();
            self::$_pdos_selectAll->execute();
            // résultat du fetch dans une instance de fonction_prix
            $lesLivres = self::$_pdos_selectAll->fetchAll(PDO::FETCH_CLASS,'fonction_prix');
            return $lesLivres;
        }
        catch (PDOException $e) {
            print($e);
        }
    }


    /**
     * initialisation d'un objet métier à partir d'un enregistrement de Ceremonie
     * @param $id_ceremonie un identifiant de Ceremonie
     * @return l'instance de fonction_ceremonie associée à $id_ceremonie
     */ 
    public static function initfonction_cerenomie($id_ceremonie) : fonction_ceremonie {
        try {
            if (!isset(self::$_pdo))
                self::initPDO();
            if (!isset(self::$_pdos_select))
                self::initPDOS_select();
            self::$_pdos_select->bindValue(':numero',$id_ceremonie);
            self::$_pdos_select->execute();
        // résultat du fetch dans une instance de fonction_prix
            $lm = self::$_pdos_select->fetchObject('fonction_ceremonie');
            if (isset($lm) && ! empty($lm))
                $lm->setNouveau(FALSE);
            if (empty($lm))
                throw new Exception("Ceremonie $id_ceremonie inexistant dans la table Ceremonie.\n");
            return $lm;
        }
        catch (PDOException $e) {
            print($e);
        }
    }

    /**
     * sauvegarde d'un objet métier
     * soit on insère un nouvel objet
     * soit on le met à jour
     */ 
    public function save() : void {
        if (!isset(self::$_pdo))
            self::initPDO();
        if ($this->nouveau) {
            if (!isset(self::$_pdos_insert)) {
                self::initPDOS_insert();
            }
            self::$_pdos_insert->bindParam(':numero', $this->id_ceremonie);
            self::$_pdos_insert->bindParam(':nom', $this->nom_ceremonie);
            self::$_pdos_insert->bindParam(':organisation', $this->id_organisation);
            self::$_pdos_insert->execute();
            $this->setNouveau(FALSE);
        }
        else {
            if (!isset(self::$_pdos_update))
                self::initPDOS_update();
            self::$_pdos_update->bindParam(':numero', $this->id_ceremonie);
            self::$_pdos_update->bindParam(':nom', $this->nom_ceremonie);
            self::$_pdos_update->bindParam(':organisation', $this->id_organisation);
            self::$_pdos_update->execute();
        }
    }

    /**
     * suppression d'un objet métier
     */ 
    public function delete() :void {
        if (!isset(self::$_pdo))
            self::initPDO();
        if (!$this->nouveau) {
            if (!isset(self::$_pdos_delete)) {
                self::initPDOS_delete();
            }
            self::$_pdos_delete->bindParam(':numero', $this->id_ceremonie);
            self::$_pdos_delete->execute();
        }
        $this->setNouveau(TRUE);
    }

    /**
     * nombre d'objets metier disponible dans la table
     */
    public static function getNbAlbum() : int {
        if (!isset(self::$_pdos_count)) {
            self::initPDOS_count();
        }
        self::$_pdos_count->execute();
        $resu = self::$_pdos_count->fetch();
        return $resu[0];
    }



    /**
     * affichage élémentaire
     */ 
    public function __toString() : string {
        $ch = "<table border='1'><tr><th>id_ceremonie</th><th>nom_ceremonie</th><th>id_organisation</th><th>nouveau</th></tr><tr>";
        $ch.= "<td>".$this->id_ceremonie."</td>";
        $ch.= "<td>".$this->nom_ceremonie."</td>";
        $ch.= "<td>".$this->id_organisation."</td>";
        $ch.= "<td>".$this->nouveau."</td>";
        $ch.= "</tr></table>";
        return $ch;
    }
}
    
?>