<?php
abstract class Personnage
{
  protected $degats,
						$id,
						$nom,
						$experience,
						$level,
						$forcePersonnage,
						$nbCoups,
						$dernierCoup,
						$timeEndormi,
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
    if ($perso->id() == $this->id)
    {
      return self::CEST_MOI;
    }
		
		// On donne de l'expérience au perso qui frappe
		$this->recevoirExperience(5);
    
    // On indique au personnage qu'il doit recevoir des dégâts.
    // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE
    switch ($this->type())
    {
      case 'brute': return $perso->recevoirDegats($this->forcePersonnage + $this->atout); break;
      default: return $perso->recevoirDegats($this->forcePersonnage); break;
    }
  }
  
  public function recevoirDegats(int $bonus)
  {
    $this->degats += 5 + $bonus;
    
    // Si on a 100 de dégâts ou plus, on dit que le personnage a été tué.
    if ($this->degats >= 100)
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
			$this->experience = 0;
		}
		else
		{
			$this->experience += $experience;
		}
	}
	
	public function enregistrerCoup()
	{
		$this->nbCoups += 1;
		$this->dernierCoup = new DateTime();
	}
	
	public function levelUp() {
		if ($this->level < 100)
		{
			$this->level += 1;
			$this->forcePersonnage += 2;
		}
	}
	
	public function nomValide()
	{
		return !empty($this->nom);
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
		//var_dump($this->atout);
  }
  
  function timeEndormiString(int $delai)
  {
    $delai_txt = "";
    $nb_tmp = intval($delai / 3600);
    if ($nb_tmp > 0) { // HOURS
      $delai_txt .= " " . $nb_tmp . " h";
      $delai -= 3600 * $nb_tmp;
    }
    $nb_tmp = intval($delai / 60);
    if ($nb_tmp > 0) { // MINUTES
      $delai_txt .= " " . $nb_tmp . " min";
      $delai -= 60 * $nb_tmp;
    }
    return $delai_txt . " " . $delai;
  }
  
  
  // GETTERS //
  
  public function degats()
  {
    return $this->degats;
  }
  
  public function id()
  {
    return $this->id;
  }
  
  public function nom()
  {
    return $this->nom;
  }
  
  public function experience()
  {
    return $this->experience;
  }
  
  public function level()
  {
    return $this->level;
  }
  
  public function forcePersonnage()
  {
    return $this->forcePersonnage;
  }
  
  public function nbCoups()
  {
    return $this->nbCoups;
  }
  
  public function dernierCoup()
  {
    return $this->dernierCoup;
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
      $this->degats = $degats;
    }
  }
  
  public function setId($id)
  {
    $id = (int) $id;
    
    if ($id > 0)
    {
      $this->id = $id;
    }
  }
  
  public function setNom($nom)
  {
    if (is_string($nom))
    {
      $this->nom = $nom;
    }
  }
  
  public function setExperience($experience)
  {
    $experience = (int) $experience;
    
    if ($experience >= 0 && $experience < 100)
    {
      $this->experience = $experience;
    }
  }
  
  public function setLevel($level)
  {
    $level = (int) $level;
    
    if ($level >= 1 && $level < 100)
    {
      $this->level = $level;
    }
  }
  
  public function setForcePersonnage($forcePersonnage)
  {
    $forcePersonnage = (int) $forcePersonnage;
    
    if ($forcePersonnage >= 0 && $forcePersonnage <= 200)
    {
      $this->forcePersonnage = $forcePersonnage;
    }
  }
  
  public function setNbCoups($nbCoups)
  {
    $nbCoups = (int) $nbCoups;
    
    if ($nbCoups >= 0 && $nbCoups <= 3)
    {
      $this->nbCoups = $nbCoups;
    }
  }
  
  public function setDernierCoup($dernierCoup)
  {
    if (self::validateDate($dernierCoup))
    {
      $this->dernierCoup = $dernierCoup;
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
    if ($type == "magicien" || $type == "guerrier" || $type == "brute" || $type == "sorcier")
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