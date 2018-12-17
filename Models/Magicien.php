<?php
class Magicien extends Personnage
{
  const MANA_EMPTY = 2;
	const SORT_REUSSI = 3;
	
	public function ensorceler(Personnage $perso)
	{
    if ($perso->id == $this->id)
    {
      return self::CEST_MOI;
    }
		
		if ($this->atout() <= 0)
		{
			return self::MANA_EMPTY;
		}
		
		return $perso->endormir($this->atout() * 6 * 3600);
	}
	
	public function endormir($duree)
	{
		$this->timeEndormi = time() + $duree;
		return self::SORT_REUSSI;
	}
}