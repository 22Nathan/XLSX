<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Database\Seeder;

class Home extends Controller
{ 

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public function index()
    {

        //load the forge class ( managing database )
        $forge = \Config\Database::forge();

        //
        $db = \Config\Database::connect();
        
        //vider la database
        $tables = $db->listTables();
        foreach ($tables as $table) 
        {
            $forge->dropTable( $table , false , true );
        }

        $messageErreur = "";

        $data = [
            'messageErreur' => $messageErreur,
        ];

        return view('welcome_message', $data);
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public function convert()
    {

        //load the forge class ( managing database )
        $forge = \Config\Database::forge();

        //
        $db = \Config\Database::connect();

        $rmfiles = glob( WRITEPATH.'../public/csvfile'.'/*' );
        foreach($rmfiles as $f) {
            if(is_file($f)) 
            {
                unlink($f); 
            }    
        }

        $input = $this->validate([
            'file' => 'uploaded[file]|max_size[file,2048]|ext_in[file,csv],'
        ]);

        if (!$input) 
        {
            //$data['validation'] = $this->validator;
            $messageErreur = "Erreur de lecture du fichier <br> vérifier son extension et son intégrité <br><br>";

            $data = [
                'messageErreur' => $messageErreur,
            ];

            //retorune la vue d'acceuil 
            return view('welcome_message', $data);
        }
        else
        {
            //récupération du fichier
            $file = $this->request->getFile('file');

            //get le nom du fichier
            $namefile = strval($file->getName()) ;

            //Si il y a un fichier
            if( $file ) 
            {
                //Si le fichier est valide et n'a pas été déplacé
                if( $file->isValid() && !$file->hasMoved() ) 
                {
                    
                    //générer un nom aléatoire
                    $newName = $file->getRandomName();

                    //déplacer le fichier et le renomer
                    $file->move(WRITEPATH.'../public/csvfile', $newName);

                    //get file location
                    $filelocation = WRITEPATH."../public/csvfile/".$newName;

                    //get instance of file
                    $file = new \CodeIgniter\Files\File( $filelocation );

                    //ouvrir le fichier
                    $fileopen = fopen( $file, "r" );

                    //get première ligne du fichier
                    $line = fgets( $fileopen );

                    //déterminer le séparateur ; ou ,
                    if( strpos( $line , ";" ) == true && strpos( $line , "," ) == false )
                    {
                        $separateur = ";";
                    }
                    else
                    {
                        $separateur = ",";
                    }

                    //tableau avec les champs de la 1ère ligne
                    $tabfields = explode( $separateur , $line );

                    //nb de fields
                    $numberOfFields = count( $tabfields );

                    //créer les colonnes

                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                    //création de l'id
                    /*
                    $fields = 
                    [
                        'id' => [ 'type' => 'INT', 'constraint' => 10, 'auto_increment' => true, 'unique' => true ]
                    ];

                    //ajout le field à la future table
                    $forge->addField( $fields );
                    */
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

                    //boucler sur le nombre de colonnes
                    for( $i = 0 ; $i < $numberOfFields ; $i++ )
                    {
                        //si il y a le caractère "\n"
                        if( strpos( $tabfields[$i] , "\n" ) !== false )
                        {
                            //supprimer ce caractère
                            $tabfields[$i] = str_replace( "\n" , "" , $tabfields[$i] );
                        }
                        //si il y a le caractère "\r"
                        if( strpos( $tabfields[$i] , "\r" ) !== false )
                        {
                            //supprimer ce caractère
                            $tabfields[$i] = str_replace( "\r" , "" , $tabfields[$i] );
                        }

                        $fields =
                        [
                            //formater la colonne pour insérer dans la table sql
                            $tabfields[$i] => [ 'type' => 'VARCHAR', 'constraint' => 255 ]
                        ];

                        //insérer dans la table
                        $forge->addField( $fields );
                    }

                    //get extension
                    $type = $file->guessExtension();

                    //retirer l'extension au nom de la table
                    if( strpos( $namefile , $type ) !== false )
                    {
                        $namefile = str_replace( '.'.$type , "" , $namefile );
                    }


                    //créer la table
                    if( !$db->tableExists($namefile) )
                    {
                        $forge->createTable( $namefile , true );
                        $reponseTrue = " Table créée avec succès. " ;
                        $reponseFalse = "" ;

                        //
                        $builder = $db->table( $namefile );

                        if( $fileopen )
                        {
                            $i = 0 ;
                            $arr = [];

                            //supprimer les caractères d'échappement
                            while( ( $buffer = fgets( $fileopen ) ) !== false )
                            {
                                if( strpos( $buffer , "\n" ) !== false )
                                {
                                    $buffer = str_replace( "\n" , "" , $buffer );
                                }
                                if( strpos( $buffer , "\r" ) !== false )
                                {
                                    $buffer = str_replace( "\r" , "" , $buffer );
                                }

                                //séparer les lignes en array en fonction du séparateur
                                $arr[$i] = explode( $separateur , $buffer );

                                for( $z = 0 ; $z < $numberOfFields ; $z++ )
                                {
                                    $arr[$i][$tabfields[$z]] = $arr[$i][$z];
                                }

                                $arr[$i] = array_slice( $arr[$i], -$numberOfFields );
                                //var_dump( $arr[$i] );


                                $builder->insert( $arr[$i] );

                                $i++ ;
                                
                            }
                        }

                        $table = new \CodeIgniter\View\Table();

                    }
                    else
                    {
                        $reponseTrue = "" ;
                        $reponseFalse = " Erreur table déjà existante ! <br> Renommer votre fichier et réessayer. " ;

                        $tabfields = [];
                        $arr = [];
                        $table = null;
                    }

                    fclose( $fileopen );

                    if( $filelocation )
                    {
                        unlink( $filelocation );
                    }
                    
                }
            }
        }

        //---
        //Initialiser la session et y affecter la table compléte ( colonnes + valeurs )
        $coloneEtlignes = 
        [
            'colonnes' => $tabfields,
            'lignes'   => $arr,
        ];


        $session = \Config\Services::session($config);
        $session->set($coloneEtlignes);
        //---
        
        $data = [
            'reponseT' => $reponseTrue,
            'reponseF' => $reponseFalse,
            'colonnes' => $tabfields,
            'result'   => $arr,
            'table'    => $table,
        ];

        echo view('convert', $data);
    }    

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public function update()
    {

        $table = new \CodeIgniter\View\Table();
        $session = session();

        if( $session )
        {
            $colonnes = $session->get('colonnes');
            $lignes   = $session->get('lignes');
        }
        else
        {
            $colonnes = null;
            $lignes   = null;
        }

        $nbColonnes = count( $colonnes );
        $session->set('nbColonnes' , $nbColonnes);

        //table de base
        $tableDeBase = [
            'TDBcolonnes'   => $colonnes,
            'TDBlignes'     => $lignes,
            'TDBnbColonnes' => $nbColonnes,  
        ];
        $session->set( $tableDeBase );

        $data = [
            'nbCol'    => $nbColonnes,
            'table'    => $table,
            'colonnes' => $colonnes,
            'lignes'   => $lignes,
        ];

        echo view( 'update', $data );
    }




    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public function delCol()
    {
        $session = session();
        $table = new \CodeIgniter\View\Table();

        $colonnes   = $session->get('colonnes');
        $lignes     = $session->get('lignes');
        $nbColonnes = $session->get('nbColonnes');

        $delCol = true;
        $delLignes = null;

        if( $_POST )
        {
            //supprimer les colonnes
            $post = array_keys( $_POST );

            for( $i = 0 ; $i < count($post) ; $i++ )
            {
                $position = array_search( $post[$i] , $colonnes );
                //array_splice( $colonnes , $position , ($position+1) );
                array_splice( $colonnes , $position , 1 );
                for( $j = 0 ; $j < count( $lignes ) ; $j++ )
                {
                    //array_splice( $lignes[$j] , $position , ($position+1) );
                    array_splice( $lignes[$j] , $position , 1 );
                    
                }
            }

            $delCol = null;
        }

        $session->set( 'colonnes'   , $colonnes );
        $session->set( 'lignes'     , $lignes  );
        $session->set( 'nbColonnes' , $nbColonnes );

        $data = [
            'table'    => $table,
            'colonnes' => $colonnes,
            'lignes'   => $lignes,
            'delCol'   => $delCol,
            'nbColonnes' => $nbColonnes,
            'delLignes'  => $delLignes,
        ];

        echo view( 'update', $data );
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public function addCol()
    {
        $session = session();
        $table = new \CodeIgniter\View\Table();

        $colonnes   = $session->get('colonnes');
        $lignes     = $session->get('lignes');
        $nbColonnes = $session->get('nbColonnes');

        $txtColonnes = "Colonne_n°" . ($nbColonnes +1) ;

        array_push( $colonnes, $txtColonnes );

        for( $i = 0 ; $i < count($lignes) ; $i++ )
        {
            array_push( $lignes[$i] , "..." );
        }

        $session->set( 'colonnes' , $colonnes );
        $session->set( 'lignes'  , $lignes  );
        $session->set( 'nbColonnes' , $nbColonnes+1 );

        $data = [
            'table'    => $table,
            'colonnes' => $colonnes,
            'lignes'   => $lignes,
        ];

        return view( 'update', $data );
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public function delLine()
    {
        $session = session();
        $table = new \CodeIgniter\View\Table();

        $colonnes   = $session->get('colonnes');
        $lignes     = $session->get('lignes');
        $nbColonnes = $session->get('nbColonnes');

        $delLignes = true;
        $delCol = null;

        if( $_POST )
        {
            //supprimer les lignes
            $post = array_keys( $_POST );

            for( $i = 0 ; $i < count($post) ; $i++ )
            {
                array_splice( $lignes[$post[$i]] , 0 );             
            }

            $delLignes = null;
        }

        $session->set( 'colonnes'   , $colonnes );
        $session->set( 'lignes'     , $lignes  );
        $session->set( 'nbColonnes' , $nbColonnes );

        $data = [
            'table'     => $table,
            'colonnes'  => $colonnes,
            'lignes'    => $lignes,
            'delLignes' => $delLignes,
        ];

        echo view( 'update', $data );
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public function addLine()
    {
        $session = session();
        $table = new \CodeIgniter\View\Table();

        $colonnes   = $session->get('colonnes');
        $lignes     = $session->get('lignes');
        $nbColonnes = $session->get('nbColonnes');

        $unArray = [];

        for( $i = 0 ; $i < count($colonnes) ; $i++ )
        {
            //array_push( $lignes[$i] , "..." );
            $unArray[$colonnes[$i]] = "...";
        }        
        
        array_push( $lignes , $unArray );

        $session->set( 'colonnes' , $colonnes );
        $session->set( 'lignes'  , $lignes  );
        
        $data = [
            'table'    => $table,
            'colonnes' => $colonnes,
            'lignes'   => $lignes,
        ];

        echo view( 'update', $data );
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public function save()
    {
        $reponseTrue = "Changements chargés avec succès";
        $reponseFalse = "";

        $session = session();
        $table = new \CodeIgniter\View\Table();

        $colonnes   = $session->get('colonnes');
        $lignes     = $session->get('lignes');

        $data = [
            'table'    => $table,
            'colonnes' => $colonnes,
            'result'   => $lignes,
            'reponseT' => $reponseTrue,
            'reponseF' => $reponseFalse,
        ];

        echo view( 'convert', $data );
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public function cancel()
    {
        $reponseTrue = "Changements annulés";
        $reponseFalse = "";

        $session = session();
        $table = new \CodeIgniter\View\Table();

        $colonnes   = $session->get('TDBcolonnes');
        $lignes     = $session->get('TDBlignes');

        $session->set( 'colonnes' , $colonnes );
        $session->set( 'lignes' , $lignes );

        $data = [
            'table'    => $table,
            'colonnes' => $colonnes,
            'result'   => $lignes,
            'reponseT' => $reponseTrue,
            'reponseF' => $reponseFalse,
        ];

        echo view( 'convert', $data );
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}
