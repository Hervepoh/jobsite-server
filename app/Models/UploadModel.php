<?php 

namespace App\Models;

use CodeIgniter\Model;
use App\Models\DataModel;

use CodeIgniter\Files\File;


/**
 * Modèles de requête sql pour mysql avec codeigniter
 * Toutes les requêtes qui permette de télécharger les fichiers et de les sauvegarder
 * Les liens sont sauvegardés dans la base de données 
 * Les fichiers sont sauvegardés dans un répertoire du dossier de l'application : 
 * ../web/upload/audio ou document ou image ou video
**/ 
class UploadModel extends Model
{
	// tables
	protected $t_mediatype = 't_types_fichiers';
	protected $t_media = 't_fichiers';
	private $model = null;

	public function upload($img)
	{
        $this->model = new DataModel();
		
		$last_doc=0;
		if (! $img->hasMoved()) 
		{
			$filepath = WRITEPATH . 'uploads/' . $img->store();
			$data = ['uploaded_fileinfo' => new File($filepath)];
			
			$datamediaInsert = array('file_media'=>$data['uploaded_fileinfo'], //$result['upload_data']['file_name'],
									 'date_create'=> time(),
									 'id_type_media'=> 7, //$chemin[0]->id_media_type,
									);
									
			$last_doc = $this->model->insert_data($this->t_media, $datamediaInsert);
			return $last_doc;
		}
	}

}
