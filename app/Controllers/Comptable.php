<?php namespace App\Controllers;

use CodeIgniter\Controller;
use \App\Models\Authentif;
use \App\Models\ActionsComptable;

/**
 * Contrôleur du module VISITEUR de l'application
*/
class Comptable extends BaseController {

   private $authentif;
   private $actComptable;
   private $idComptable;
   
   private function checkAuth()
   {
            // contrôle de la bonne authentification de l'utilisateur
            // TODO : 	Lorsque des comptables utiliseront cette même application, il faudra enrichir 
            //			ce code. En effet les comptables n'ont pas le droit d'accéder à ce code et, par
            //			ailleurs, les visiteurs n'auront pas d'accès au controleur des comptables !!!
            $this->authentif = new Authentif();
            if (!$this->authentif->estConnecte()) 
            {
                    $res = false;
            }
            else 
            {
                    $this->actComptable = new ActionsComptable();

                    $this->session = session();
                    $this->idComptable = $this->session->get('idUser');
                    $this->actComptable->checkLastSix($this->idComptable);
                    $res = true;
            }
            return $res;
    }

   private function unauthorizedAccess()
   {
		// l'accès à ce contrôleur n'est pas autorisé : on renvoie une vue erreur
		return view ('errors/html/error_401');
		// on aurait aussi  pu renvoyer vers le contrôleur par défaut comme suit : 
		//return redirect()->to('/applifrais-CI4/public/anonyme');
   }

    public function index()
    {
            if (!$this->checkAuth()) return $this->unauthorizedAccess();
            // envoie de la vue accueil du visiteur
            $data['identite'] = $this->session->get('prenom').' '.$this->session->get('nom');

            $data['statut'] = $this->actComptable->leStatut($this->session->get('idUser'));
            if ($data['statut'] == 'Comptable')
            {
                return view('v_comptable/v_comptableAccueil', $data);
            }
            return view('v_comptable/v_comptableAccueil', $data);
    }

    public function lesFiches($message = "")
    {
            if (!$this->checkAuth()) return $this->unauthorizedAccess();
            $data['identite'] = $this->session->get('prenom').' '.$this->session->get('nom');
            $data['statut'] = $this->actComptable->leStatut($this->session->get('idUser'));

            $data['mesFiches'] = $this->actComptable->getLesFichesDesVisiteurs();
            $data['notify'] = $message;  
            return view('v_comptable/v_comptableLesFiches', $data);	
    }

    public function seDeconnecter()	
    {
            if (!$this->checkAuth()) return $this->unauthorizedAccess();
            return $this->authentif->deconnecter();
    }

    public function voirMaFiche($mois)
    {	// TODO : contrôler la validité du paramètre (mois de la fiche à consulter)

            if (!$this->checkAuth()) return $this->unauthorizedAccess();
            $data['identite'] = $this->session->get('prenom').' '.$this->session->get('nom');
            $data['statut'] = $this->actComptable->leStatut($this->session->get('idUser'));

            $data['mois'] = $mois;
            $data['fiche'] = $this->actComptable->getUneFiche($this->idComptable, $mois);

            return view('v_comptable/v_comptableVoirFiche', $data);
    }

    public function modMaFiche($mois, $message = "")
    {	// TODO : contrôler la validité du second paramètre (mois de la fiche à modifier)

            if (!$this->checkAuth()) return $this->unauthorizedAccess();
            $data['identite'] = $this->session->get('prenom').' '.$this->session->get('nom');
            $data['statut'] = $this->actComptable->leStatut($this->session->get('idUser'));

            $data['notify'] = $message;
            $data['mois'] = $mois;
            $data['fiche'] = $this->actComptable->getUneFiche($this->idComptable, $mois);
            $data['etat'] = $this->actComptable->getLesEtats();
            return view('v_comptable/v_comptableModFiche', $data);
    }

    public function signeMaFiche($mois)
    {	// TODO : contrôler la validité du second paramètre (mois de la fiche à modifier)

            if (!$this->checkAuth()) return $this->unauthorizedAccess();
            $this->actComptable->signeFiche($this->idComptable, $mois);

            // ... et on revient à mesFiches
            return $this->mesFiches("La fiche $mois a été signée. <br/>Pensez à envoyer vos justificatifs afin qu'elle soit traitée par le service comptable rapidement.");
    }
    public function signeLaFiche($id, $mois)
    {	// TODO : contrôler la validité du second paramètre (mois de la fiche à modifier)

            if (!$this->checkAuth()) return $this->unauthorizedAccess();
            $this->actComptable->signeFiche($id, $mois);
            // ... et on revient à lesFiches
            return $this->lesFiches("Fiche validée $mois $id.");
    }
    public function refuseLaFiche($id, $mois)
    {	// TODO : contrôler la validité du second paramètre (mois de la fiche à modifier)

            if (!$this->checkAuth()) return $this->unauthorizedAccess();
            $comment = $this->request->getPost('login');
            if ($comment == null)
            {
                return $this->lesFiches("Impossible de refuser la fiche $mois de $id ! Il faut mettre un commentaire !.");

            }
            
            $this->actComptable->refuseFiche($id, $mois, $comment);
            
            // ... et on revient à lesFiches
            return $this->lesFiches("La fiche $mois de $id est refusée !");

    }
    public function majForfait($mois)
    {	// TODO : conrôler que l'obtention des données postées ne rend pas d'erreurs
            // TODO : dans la dynamique de l'application, contrôler que l'on vient bien de modFiche

            if (!$this->checkAuth()) return $this->unauthorizedAccess();
            // obtention des données postées
            $lesFrais = $this->request->getPost('lesFrais');

            $this->actComptable->majForfait($this->idComptable, $mois, $lesFrais);

            // ... et on revient en modification de la fiche
            return $this->modMaFiche($mois, 'Modification(s) des éléments forfaitisés enregistrée(s) ...');
    }

    public function ajouteUneLigneDeFrais($mois)
    {	// TODO : conrôler que l'obtention des données postées ne rend pas d'erreurs
            // TODO : dans la dynamique de l'application, contrôler que l'on vient bien de modFiche

            if (!$this->checkAuth()) return $this->unauthorizedAccess();
            // obtention des données postées
            $uneLigne = array( 
                    'dateFrais' => $this->request->getPost('dateFrais'),
                    'libelle' => $this->request->getPost('libelle'),
                    'montant' => $this->request->getPost('montant')
            );
            $this->actComptable->ajouteFrais($this->idComptable, $mois, $uneLigne);

            // ... et on revient en modification de la fiche
            return $this->modMaFiche($mois, 'Ligne "Hors forfait" ajoutée ...');				
    }

    public function supprUneLigneDeFrais($mois, $idLigneFrais)
    {	// TODO : contrôler la validité du second paramètre (mois de la fiche à modifier)
            // TODO : dans la dynamique de l'application, contrôler que l'on vient bien de modFiche

            if (!$this->checkAuth()) return $this->unauthorizedAccess();
            // l'id de la ligne à supprimer doit avoir été transmis en second paramètre
            $this->actComptable->supprFrais($this->idComptable, $mois, $idLigneFrais);

            // ... et on revient en modification de la fiche
            return $this->modMaFiche($mois, 'Ligne "Hors forfait" supprimée ...');				
    }
}