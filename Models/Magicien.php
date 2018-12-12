<?php
class Magicien extends Personnage
{
  const MANA_EMPTY = 2;
	const SORT_REUSSI = 3;
	
	public function ensorceler(Personnage $perso)
	{
    if ($perso->_id == $this->_id)
    {
      return self::CEST_MOI;
    }
		
		if ($this->atout() <= 0)
		{
			return self::MANA_EMPTY;
		}
		
		return $perso->endormir();
	}
	
	public function endormir()
	{
		$this->timeEndormi = time();
		return self::SORT_REUSSI;
	}
}