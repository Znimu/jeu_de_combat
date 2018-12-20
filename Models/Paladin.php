<?php
class Paladin extends Personnage
{
    const MANA_EMPTY = 2;
    const SORT_REUSSI = 3;

    function soigner(Personnage $perso)
    {
        if ($perso->id() == $this->id)
        {
            return self::CEST_MOI;
        }
		
		if ($this->atout() <= 0)
		{
			return self::MANA_EMPTY;
		}
		
		// On donne de l'expérience au perso qui soigne
        $this->recevoirExperience(5);
        
        $perso->degats -= 5;
        if ($perso->degats < 0)
        {
            $perso->degats = 0;
        }
		return self::SORT_REUSSI;
	}
}