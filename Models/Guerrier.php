<?php
class Guerrier extends Personnage
{
  public function recevoirDegats(int $bonus)
  {
    $this->degats += 5 + $bonus - $this->atout();
    
    // Si on a 100 de dégâts ou plus, on dit que le personnage a été tué.
    if ($this->degats >= 100)
    {
      return self::PERSONNAGE_TUE;
    }
		
		$this->updateAtout(); // On met à jour l'atout suivant les dégâts
		
    // Sinon, on se contente de dire que le personnage a bien été frappé.
    return self::PERSONNAGE_FRAPPE;
  }
}