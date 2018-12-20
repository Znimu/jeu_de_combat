<?php
class Sorcier extends Personnage
{
    const MANA_EMPTY = 2;
    const SORT_REUSSI = 3;

    public function bouleDeFeu(Personnage $perso)
    {
        if ($perso->id == $this->id)
        {
        return self::CEST_MOI;
        }
            
        if ($this->atout() <= 0)
        {
            return self::MANA_EMPTY;
        }
        
        return self::SORT_REUSSI;
    }
}