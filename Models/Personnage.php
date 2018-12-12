<?php
class Personnage
{
  protected $_degats,
						$_id,
						$_nom,
						$_experience,
						$_level,
						$_forcePersonnage,
						$_nbCoups,
						$_dernierCoup;
	
	protected	$timeEndormi,
						$type,
						$atout;
  
  const CEST_MOI = 1; // Constante renvoyée par la méthode `frapper` si on se frappe soi-même.
  const PERSONNAGE_TUE = 2; // Constante renvoyée par la méthode `frapper` si on a tué le personnage en le frappant.
  const PERSONNAGE_FRAPPE = 3; // Constante renvoyée par la méthode `frapper` si on a bien frappé le personnage.
  const COUPS_EPUISES = 4; // Constante renvoyée par la méthode `frapper` si on a déjà donné 3 coups aujourd'hui.
  
  
  public function __construct(array $donnees)
  {
    $this->hydrate($donnees);
  }
  
  public function hydrate(array $donnees)
  {
    foreach ($donnees as $key => $value)
    {
      $method = 'set'.ucfirst($key);
      
      if (method_exists($this, $method))
      {
        $this->$method($value);
      }
    }
  }
  
  public function frapper(Personnage $perso)
  {
    if ($perso->id() == $this->_id)
    {
      return self::CEST_MOI;
    }
		
		// On donne de l'expérience au perso qui frappe
		$this->recevoirExperience(5);
    
    // On indique au personnage qu'il doit recevoir des dégâts.
    // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE
    return $perso->recevoirDegats($this->_forcePersonnage);
  }
  
  public function recevoirDegats(int $bonus)
  {
    $this->_degats += 5 + $bonus;
    
    // Si on a 100 de dégâts ou plus, on dit que le personnage a été tué.
    if ($this->_degats >= 100)
    {
      return self::PERSONNAGE_TUE;
    }
		
		$this->updateAtout(); // On met à jour l'atout suivant les dégâts
		
    // Sinon, on se contente de dire que le personnage a bien été frappé.
    return self::PERSONNAGE_FRAPPE;
  }
	
	public function recevoirExperience(int $experience)
	{
		if ($this->experience() + $experience >= 100) {
			$this->levelUp();
			$this->_experience = 0;
		}
		else
		{
			$this->_experience += $experience;
		}
	}
	
	public function enregistrerCoup()
	{
		$this->_nbCoups += 1;
		$this->_dernierCoup = new DateTime();
	}
	
	public function levelUp() {
		if ($this->_level < 100)
		{
			$this->_level += 1;
			$this->_forcePersonnage += 2;
		}
	}
	
	public function nomValide()
	{
		return !empty($this->_nom);
	}
	
	public static function validateDate($date, $format = 'Y-m-d')
	{
		$d = DateTime::createFromFormat($format, $date);
		// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		return $d && $d->format($format) === $date;
	}
	
	public function updateAtout()
	{
		if ($this->degats() >= 0 && $this->degats() < 25)
		{
			$this->atout = 4;
		}
		elseif ($this->degats() >= 25 && $this->degats() < 50)
		{
			$this->atout = 3;
		}
		elseif ($this->degats() >= 50 && $this->degats() < 75)
		{
			$this->atout = 2;
		}
		elseif ($this->degats() >= 75 && $this->degats() < 90)
		{
			$this->atout = 1;
		}
		else
		{
			$this->atout = 0;
		}
		var_dump($this->atout);
	}
  
  
  // GETTERS //
  
  public function degats()
  {
    return $this->_degats;
  }
  
  public function id()
  {
    return $this->_id;
  }
  
  public function nom()
  {
    return $this->_nom;
  }
  
  public function experience()
  {
    return $this->_experience;
  }
  
  public function level()
  {
    return $this->_level;
  }
  
  public function forcePersonnage()
  {
    return $this->_forcePersonnage;
  }
  
  public function nbCoups()
  {
    return $this->_nbCoups;
  }
  
  public function dernierCoup()
  {
    return $this->_dernierCoup;
  }
  
  public function timeEndormi()
  {
    return $this->timeEndormi;
  }
  
  public function type()
  {
    return $this->type;
  }
  
  public function atout()
  {
    return $this->atout;
  }
	
	// SETTERS //
  
  public function setDegats($degats)
  {
    $degats = (int) $degats;
    
    if ($degats >= 0 && $degats <= 100)
    {
      $this->_degats = $degats;
    }
  }
  
  public function setId($id)
  {
    $id = (int) $id;
    
    if ($id > 0)
    {
      $this->_id = $id;
    }
  }
  
  public function setNom($nom)
  {
    if (is_string($nom))
    {
      $this->_nom = $nom;
    }
  }
  
  public function setExperience($experience)
  {
    $experience = (int) $experience;
    
    if ($experience >= 0 && $experience < 100)
    {
      $this->_experience = $experience;
    }
  }
  
  public function setLevel($level)
  {
    $level = (int) $level;
    
    if ($level >= 1 && $level < 100)
    {
      $this->_level = $level;
    }
  }
  
  public function setForcePersonnage($forcePersonnage)
  {
    $forcePersonnage = (int) $forcePersonnage;
    
    if ($forcePersonnage >= 0 && $forcePersonnage <= 200)
    {
      $this->_forcePersonnage = $forcePersonnage;
    }
  }
  
  public function setNbCoups($nbCoups)
  {
    $nbCoups = (int) $nbCoups;
    
    if ($nbCoups >= 0 && $nbCoups <= 3)
    {
      $this->_nbCoups = $nbCoups;
    }
  }
  
  public function setDernierCoup($dernierCoup)
  {
    if (self::validateDate($dernierCoup))
    {
      $this->_dernierCoup = $dernierCoup;
    }
  }
  
  public function setTimeEndormi($timeEndormi)
  {
		$timeEndormi = intval($timeEndormi);
		
    $this->timeEndormi = $timeEndormi;
  }
  
  public function setType($type)
  {
		$type = strtolower($type);
    if ($type == "magicien" || $type == "guerrier")
    {
      $this->type = $type;
    }
  }
  
  public function setAtout($atout)
  {
		$atout = intval($atout);
    if ($atout >= 0 && $atout <= 5)
    {
      $this->atout = $atout;
    }
  }
}