<?php
class Categorie {

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
	 * PreparedStatement associé à un SELECT, calcule le nombre de categorie de la table
	 * @var PDOStatement;
	 */
	private static $_pdos_count;

	/**
	 * PreparedStatement associé à un SELECT, récupère toutes les Categories
	 * @var PDOStatement;
	 */
	private static $_pdos_selectAll;

	/**
	 * Initialisation de la connexion et mémorisation de l'instance PDO dans Categorie::$_pdo
	 */
	public static function initPDO() {
		self::$_pdo = new PDO("pgsql:host=localhost;dbname=util", "util", "utilpass");
		// pour récupérer aussi les exceptions provenant de PDOStatement
		self::$_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * préparation de la requête SELECT * FROM Categorie
	 * instantiation de self::$_pdos_selectAll
	 */
	public static function initPDOS_selectAll() {
		self::$_pdos_selectAll = self::$_pdo->prepare('SELECT * FROM Categorie');
	}

	/**
	 * méthode statique instanciant Categorie::$_pdo_select
	 */
	public static function initPDOS_select_categorie() {
		self::$_pdos_select = self::$_pdo->prepare('SELECT * FROM Categorie WHERE id_categorie= :id_categorie');
	}

	/**
	 * méthode statique instanciant Categorie::$_pdo_select
	 */
	public static function initPDOS_select_titre() {
		self::$_pdos_select = self::$_pdo->prepare('SELECT * FROM Categorie WHERE titre_categorie= :titre');
	}

	/**
	 * méthode statique instanciant Categorie::$_pdo_select
	 */
	public static function initPDOS_select_prix() {
		self::$_pdos_select = self::$_pdo->prepare('SELECT * FROM Categorie WHERE id_prix= :id_prix');
	}

	/**
	 * méthode statique instanciant Categorie::$_pdo_update
	 */
	public static function initPDOS_update() {
		self::$_pdos_update =  self::$_pdo->prepare('UPDATE Categorie SET id_categorie=:id_categorie, titre_categorie=:titre, id_prix=:id_prix WHERE id_categorie = :id_categorie');
	}

	/**
	 * méthode statique instanciant Categorie::$_pdo_insert
	 */
	public static function initPDOS_insert() {
		self::$_pdos_insert = self::$_pdo->prepare('INSERT INTO Categorie VALUES(:id_categorie,:titre,:id_prix)');
	}

	/**
	 * méthode statique instanciant fonction_prix::$_pdo_delete
	 */
	public static function initPDOS_delete() {
		self::$_pdos_delete = self::$_pdo->prepare('DELETE FROM Categorie WHERE id_categorie=:id_categorie AND titre_categorie:=titre AND id_prix:=id_prix');
	}

	/**
	 * préparation de la requête SELECT COUNT(*) FROM Categorie
	 * instantiation de self::$_pdos_count
	 */
	public static function initPDOS_count() {
		if (!isset(self::$_pdo))
			self::initPDO();
		self::$_pdos_count = self::$_pdo->prepare('SELECT COUNT(*) FROM Categorie');
	}

	/**
	 * identifiant de la Categorie
	 * @var string
	 */
	protected $id_categorie;

	/**
	 * titre de la catégorie
	 *   @var string
	 */
	protected $titre_categorie;

	/**
	 * id du prix
	 *   @var string
	 */
	protected $id_prix;

	/**
	 * attribut interne pour différencier les nouveaux objets des objets créés côté applicatif de ceux issus du SGBD
	 * @var bool
	 */
	private $nouveau = TRUE;

	/**
	 * @return $this->id_categorie
	 */
	public function getid_categorie() : string {
		return $this->id_categorie;
	}

	/**
	 * @param $id_categorie
	 */
	public function setid_categorie($id_categorie): void {
		$this->id_categorie=$id_categorie;
	}

	/**
	 * @return $this->titre_categorie
	 */
	public function gettitre_categorie() : string {
		return $this->titre_categorie;
	}

	/**
	 * @param $this->titre_categorie
	 */
	public function settitre_categorie($titre_categorie): void {
		$this->titre_categorie=$titre_categorie;
	}

	/**
	 * @return $this->id_prix
	 */
	public function getid_prix() : string {
		return $this->id_prix;
	}

	/**
	 * @param $id_prix
	 */
	public function setid_prix($id_prix): void {
		$this->id_prix=$id_prix;
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
	 * @return un tableau de toutes les Categories
	 */
	public static function getAll(): array {
		try {
			if (!isset(self::$_pdo))
				self::initPDO();
			if (!isset(self::$_pdos_selectAll))
				self::initPDOS_selectAll();
			self::$_pdos_selectAll->execute();
			// résultat du fetch dans une instance de Categorie
			$lesLivres = self::$_pdos_selectAll->fetchAll(PDO::FETCH_CLASS,'Categorie');
			return $lesLivres;
		}
		catch (PDOException $e) {
			print($e);
		}
	}

	/**
	 * initialisation d'un objet métier à partir d'un enregistrement de Categorie
	 * @param $id_categorie identifiant de Categorie
	 * @return l'instance de Concerne associée à $id_categorie
	 */
	public static function initCategorie($id_categorie) : Categorie {
		try {
			if (!isset(self::$_pdo))
				self::initPDO();
			if (!isset(self::$_pdos_select))
				self::initPDOS_select_categorie();
			self::$_pdos_select->bindValue(':id_categorie',$id_categorie);
			self::$_pdos_select->execute();
			// résultat du fetch dans une instance de Categorie
			$lm = self::$_pdos_select->fetchObject('Categorie');
			if (isset($lm) && ! empty($lm))
				$lm->setNouveau(FALSE);
			if (empty($lm))
				throw new Exception("categorie $id_categorie inexistant dans la table Categorie.\n");
			return $lm;
		}
		catch (PDOException $e) {
			print($e);
		}
	}

	/**
	 * initialisation d'un objet métier à partir d'un enregistrement de Categorie
	 * @param $id_prix un identifiant de Categorie
	 * @return l'instance de Categorie associée à $id_ceremonie
	 */
	public static function initCategorie_prix($id_prix) : Categorie {
		try {
			if (!isset(self::$_pdo))
				self::initPDO();
			if (!isset(self::$_pdos_select))
				self::initPDOS_select_prix();
			self::$_pdos_select->bindValue(':id_prix',$id_prix);
			self::$_pdos_select->execute();
			// résultat du fetch dans une instance de Nomination
			$lm = self::$_pdos_select->fetchObject('Categorie');
			if (isset($lm) && ! empty($lm))
				$lm->setNouveau(FALSE);
			if (empty($lm))
				throw new Exception("Prix $id_prix inexistant dans la table Categorie.\n");
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
			self::$_pdos_insert->bindParam(':id_categorie', $this->id_categorie);
			self::$_pdos_insert->bindParam(':titre', $this->titre_categorie);
			self::$_pdos_insert->bindParam(':id_prix', $this->id_prix);
			self::$_pdos_insert->execute();
			$this->setNouveau(FALSE);
		}
		else {
			if (!isset(self::$_pdos_update))
				self::initPDOS_update();
			self::$_pdos_update->bindParam(':id_categorie', $this->id_categorie);
			self::$_pdos_update->bindParam(':titre', $this->titre_categorie);
			self::$_pdos_update->bindParam(':id_prix', $this->id_prix);
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
			self::$_pdos_delete->bindParam(':id_nomination', $this->id_nomination);
			self::$_pdos_delete->bindParam(':gagnante', $this->gagnante_nomination);
			self::$_pdos_delete->bindParam(':ceremonie', $this->id_ceremonie);
			self::$_pdos_delete->bindParam(':categorie', $this->id_categorie);
			self::$_pdos_delete->execute();
		}
		$this->setNouveau(TRUE);
	}

	/**
	 * nombre d'objets metier disponible dans la table
	 */
	public static function getNbCategorie() : int {
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
		$ch = "<table border='1'><tr><th>id_categorie</th><th>titre_categorie</th><th>id_prix</th><th>nouveau</th></tr><tr>";
		$ch.= "<td>".$this->id_categorie."</td>";
		$ch.= "<td>".$this->titre_categorie."</td>";
		$ch.= "<td>".$this->id_prix."</td>";
		$ch.= "<td>".$this->nouveau."</td>";
		$ch.= "</tr></table>";
		return $ch;
	}
}
?>