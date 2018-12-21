<?php
class FrontendController
{
	public 	$db,
			$manager,
			$message,
			$typeMessage,
			$perso;

	public function __construct()
	{
		$this->db = new PDO('mysql:host=localhost;dbname=test2', 'root', '');
		$this->manager = new PersonnagesRepository($this->db);
		$this->typeMessage = "Confirm";
	}

	public function utiliserPerso()
	{
		if ($this->manager->exists($_POST['nom'])) // Si celui-ci existe.
		{
			$this->perso = $this->manager->get($_POST['nom']);
		}
		else
		{
			$this->message = '<i class="fas fa-exclamation-triangle"></i> Ce personnage n\'existe pas !'; // S'il n'existe pas, on affichera ce message.
			$this->typeMessage = "Erreur";
		}
	}

	public function creerPerso()
	{
		switch($_POST['type']) {
			case "magicien":
				$this->perso = new Magicien([
					'nom' => $_POST['nom'],
					'type' => $_POST['type']
				]);
				break;
			case "guerrier":
				$this->perso = new Guerrier([
					'nom' => $_POST['nom'],
					'type' => $_POST['type']
				]);
				break;
			case "brute":
				$this->perso = new Brute([
					'nom' => $_POST['nom'],
					'type' => $_POST['type']
				]);
				break;
			case "sorcier":
				$this->perso = new Sorcier([
					'nom' => $_POST['nom'],
					'type' => $_POST['type']
				]);
				break;
			case "paladin":
				$this->perso = new Paladin([
					'nom' => $_POST['nom'],
					'type' => $_POST['type']
				]);
				break;
			case "phoenix":
				$this->perso = new Phoenix([
					'nom' => $_POST['nom'],
					'type' => $_POST['type']
				]);
				break;
			default:
				$this->message = '<i class="fas fa-exclamation-triangle"></i> Type de personnage inconnu.';
				$this->typeMessage = "Erreur";
				break;
		}
		
		if (!$this->perso->nomValide())
		{
			$this->message = '<i class="fas fa-exclamation-triangle"></i> Le nom choisi est invalide.';
			$this->typeMessage = "Erreur";
			unset($this->perso);
		}
		elseif ($this->manager->exists($this->perso->nom()))
		{
			$this->message = '<i class="fas fa-exclamation-triangle"></i> Le nom du personnage est déjà pris.';
			$this->typeMessage = "Erreur";
			unset($this->perso);
		}
		else
		{
			$this->manager->add($this->perso);
			$this->perso = $this->manager->get($_POST['nom']);
		}
	}

	public function frapper()
	{
		if (isset($this->perso))
		{
			if ($this->manager->exists(intval($_GET['frapper'])))
			{
				$persoAFrapper = $this->manager->get(intval($_GET['frapper']));
				$resu_frappe = $this->perso->frapper($persoAFrapper);
				
				switch($resu_frappe)
				{
					case Personnage::CEST_MOI:
						$this->message = '<i class="fas fa-exclamation-triangle"></i> Impossible de se frapper soi-même !';
						$this->typeMessage = "Erreur";
						break;
						
					case Personnage::PERSONNAGE_TUE:
						$this->manager->update($this->perso);
						$this->message = '<i class="fas fa-check-circle"></i> Le personnage ' . $persoAFrapper->nom() . ' est mort !';
						$this->manager->delete($persoAFrapper);
						break;
						
					case Personnage::PERSONNAGE_FRAPPE:
						$this->manager->update($this->perso);
						$this->message = '<i class="fas fa-check-circle"></i> Le personnage ' . $persoAFrapper->nom() . ' a été frappé !';
						$this->manager->update($persoAFrapper);
						break;
						
					case Personnage::COUPS_EPUISES:
						$this->message = '<i class="fas fa-exclamation-triangle"></i> Le personnage ' . $this->perso->nom() . ' a épuisé ses frappes !';
						$this->typeMessage = "Erreur";
						break;
				}
			}
			else
			{
				$this->message = '<i class="fas fa-exclamation-triangle"></i> Impossible de trouver le personnage à frapper !';
				$this->typeMessage = "Erreur";
			}
		}
		else 
		{
			$this->message = '<i class="fas fa-exclamation-triangle"></i> Impossible de frapper sans être connecté !';
			$this->typeMessage = "Erreur";
		}
	}

	public function ensorceler()
	{
		if (!isset($this->perso))
		{
			$this->message = '<i class="fas fa-exclamation-triangle"></i> Impossible d\'ensorceler sans être connecté !';
			$this->typeMessage = "Erreur";
		}
		else
		{
			if ($this->perso->type() != "magicien")
			{
				$this->message = '<i class="fas fa-exclamation-triangle"></i> Ce personnage ne peut pas encorceler !';
				$this->typeMessage = "Erreur";
			}
			else
				{
				if (!$this->manager->exists(intval($_GET['ensorceler'])))
				{
					$this->message = '<i class="fas fa-exclamation-triangle"></i> Ce personnage à ensorceler n\'existe pas !';
					$this->typeMessage = "Erreur";
				}
				else
				{
					$persoAEnsorceler = $this->manager->get(intval($_GET['ensorceler']));
					$resu_sort = $this->perso->ensorceler($persoAEnsorceler);
					
					switch($resu_sort)
					{
						case Magicien::CEST_MOI:
							$this->message = '<i class="fas fa-exclamation-triangle"></i> Impossible de s\'ensorceler soi-même !';
							$this->typeMessage = "Erreur";
							break;
							
						case Magicien::MANA_EMPTY:
							$this->message = '<i class="fas fa-exclamation-triangle"></i> Pas assez de magie !';
							$this->typeMessage = "Erreur";
							break;
							
						case Magicien::SORT_REUSSI:
							$this->message = '<i class="fas fa-check-circle"></i> Le personnage ' . $persoAEnsorceler->nom() . ' a bien été ensorcelé !';
							$this->manager->update($persoAEnsorceler);
							break;
					}
				}
			}
		}
	}

	public function bouleDeFeu()
	{
		if (!isset($this->perso))
		{
			$this->message = '<i class="fas fa-exclamation-triangle"></i> Impossible de lancer une boule de feu sans être connecté !';
			$this->typeMessage = "Erreur";
		}
		else
		{
			if ($this->perso->type() != "sorcier")
			{
				$this->message = '<i class="fas fa-exclamation-triangle"></i> Ce personnage ne peut pas lancer de boule de feu !';
				$this->typeMessage = "Erreur";
			}
			else
				{
				if (!$this->manager->exists(intval($_GET['bouleDeFeu'])))
				{
					$this->message = '<i class="fas fa-exclamation-triangle"></i> Ce personnage à viser avec une boule de feu n\'existe pas !';
					$this->typeMessage = "Erreur";
				}
				else
				{
					$persoBouleDeFeu = $this->manager->get(intval($_GET['bouleDeFeu']));
					$resu_sort = $this->perso->bouleDeFeu($persoBouleDeFeu);
					
					switch($resu_sort)
					{
						case Sorcier::CEST_MOI:
							$this->message = '<i class="fas fa-exclamation-triangle"></i> Impossible de se se viser soi-même !';
							$this->typeMessage = "Erreur";
							break;
							
						case Sorcier::MANA_EMPTY:
							$this->message = '<i class="fas fa-exclamation-triangle"></i> Pas assez de magie !';
							$this->typeMessage = "Erreur";
							break;
							
						case Sorcier::SORT_REUSSI:
							$listePersos = $this->manager->getList($this->perso->nom());

							// Dégâts sur tous les personnages, dégâts double sur le personnage visé
							foreach ($listePersos as $unPerso)
							{
								if ($unPerso->id() != $this->perso->id()) // Pas de dégâts sur soi-même
								{
									if ($unPerso->id() == $persoBouleDeFeu->id())
									{
										$unPerso->setDegats($unPerso->degats() + $this->perso->atout() * 2);
									}
									else
									{
										$unPerso->setDegats($unPerso->degats() + $this->perso->atout());
									}
									$this->manager->update($unPerso);
								}
							}
							$this->message = '<i class="fas fa-check-circle"></i> La boule de feu a bien été lancée sur ' . $persoBouleDeFeu->nom() . ' !';
							break;
					}
				}
			}
		}
	}

	public function soigner()
	{
		if (!isset($this->perso))
		{
			$this->message = '<i class="fas fa-exclamation-triangle"></i> Impossible de soigner sans être connecté !';
			$this->typeMessage = "Erreur";
		}
		else
		{
			if ($this->perso->type() != "paladin")
			{
				$this->message = '<i class="fas fa-exclamation-triangle"></i> Ce personnage ne peut pas soigner !';
				$this->typeMessage = "Erreur";
			}
			else
				{
				if (!$this->manager->exists(intval($_GET['soigner'])))
				{
					$this->message = '<i class="fas fa-exclamation-triangle"></i> Ce personnage à soigner n\'existe pas !';
					$this->typeMessage = "Erreur";
				}
				else
				{
					$persoASoigner = $this->manager->get(intval($_GET['soigner']));
					$resu_sort = $this->perso->soigner($persoASoigner);
					
					switch($resu_sort)
					{
						case Paladin::CEST_MOI:
							$this->message = '<i class="fas fa-exclamation-triangle"></i> Impossible de se soigner soi-même !';
							$this->typeMessage = "Erreur";
							break;
							
						case Paladin::MANA_EMPTY:
							$this->message = '<i class="fas fa-exclamation-triangle"></i> Pas assez de magie !';
							$this->typeMessage = "Erreur";
							break;
							
						case Paladin::SORT_REUSSI:
							$this->message = '<i class="fas fa-check-circle"></i> Le personnage ' . $persoASoigner->nom() . ' a bien été soigné !';
							$this->manager->update($persoASoigner);
							break;
					}
				}
			}
		}
	}

	public function nbPersoEnregistres()
	{
		return $this->manager->count();
	}
}