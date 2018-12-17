<?php
class PersonnagesRepository
{
  private $_db; // Instance de PDO
  
  public function __construct($db)
  {
    $this->setDb($db);
  }
  
  public function add(Personnage $perso)
  {
    $q = $this->_db->prepare('INSERT INTO personnages(nom, type) VALUES(:nom, :type)');
    $q->bindValue(':nom', $perso->nom());
    $q->bindValue(':type', $perso->type());
    if (!$q->execute())
			echo "ERREUR CRITIQUE SQL : Personnage pas enregistré !";
    
    $perso->hydrate([
      'id' => $this->_db->lastInsertId(),
      'degats' => 0,
			'level' => 1,
			'experience' => 0,
			'forcePersonnage' => 0
    ]);
  }
  
  public function count()
  {
    return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
  }
  
  public function delete(Personnage $perso)
  {
    $this->_db->exec('DELETE FROM personnages WHERE id = '.$perso->id());
  }
  
  public function exists($info)
  {
    if (is_int($info)) // On veut voir si tel personnage ayant pour id $info existe.
    {
      return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages WHERE id = '.$info)->fetchColumn();
    }
    
    // Sinon, c'est qu'on veut vérifier que le nom existe ou pas.
    
    $q = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
    $q->execute([':nom' => $info]);
    
    return (bool) $q->fetchColumn();
  }
  
  public function get($info)
  {
    if (is_int($info))
    {
      $q = $this->_db->query('SELECT * FROM personnages WHERE id = '.$info);
      $donnees = $q->fetch(PDO::FETCH_ASSOC);
      
			if ($donnees['type'] == "magicien")
				return new Magicien($donnees);
			elseif ($donnees['type'] == "guerrier")
				return new Guerrier($donnees);
			else
				echo "ERREUR : Type inconnu !";
    }
    else
    {
      $q = $this->_db->prepare('SELECT * FROM personnages WHERE nom = :nom');
      $q->execute([':nom' => $info]);
      $donnees = $q->fetch(PDO::FETCH_ASSOC);
    
			if ($donnees['type'] == "magicien")
				return new Magicien($donnees);
			elseif ($donnees['type'] == "guerrier")
				return new Guerrier($donnees);
			else
				echo "ERREUR : Type inconnu !";
    }
  }
  
  public function getList($nom)
  {
    $persos = [];
    
    $q = $this->_db->prepare('SELECT * FROM personnages WHERE nom <> :nom ORDER BY nom');
    $q->execute([':nom' => $nom]);
    
    while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
    {
			if ($donnees['type'] == "magicien")
				$persos[] = new Magicien($donnees);
			elseif ($donnees['type'] == "guerrier")
				$persos[] = new Guerrier($donnees);
    }
    
    return $persos;
  }
  
  public function update(Personnage $perso)
  {
    $q = $this->_db->prepare('UPDATE personnages
															SET degats = :degats, experience = :experience, level = :level, forcePersonnage = :forcePersonnage, atout = :atout, timeEndormi = :timeEndormi
															WHERE id = :id');
    
    $q->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
    $q->bindValue(':experience', $perso->experience(), PDO::PARAM_INT);
    $q->bindValue(':level', $perso->level(), PDO::PARAM_INT);
    $q->bindValue(':forcePersonnage', $perso->forcePersonnage(), PDO::PARAM_INT);
    $q->bindValue(':timeEndormi', $perso->timeEndormi(), PDO::PARAM_INT);
    $q->bindValue(':atout', $perso->atout(), PDO::PARAM_INT);
    $q->bindValue(':id', $perso->id(), PDO::PARAM_INT);
    
    $q->execute();
  }
  
  public function setDb(PDO $db)
  {
    $this->_db = $db;
  }
}
?>