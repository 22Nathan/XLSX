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

<<<<<<< HEAD
=======
        //supprimer les potentiels fichiers scv
>>>>>>> b2e8714 (27/01/22)
        $rmfiles = glob( WRITEPATH.'../public/csvfile'.'/*' );
        foreach($rmfiles as $f) {
            if(is_file($f)) 
            {
                unlink($f); 
            }    
        }

<<<<<<< HEAD

=======
        //check l'extension pour du csv
>>>>>>> b2e8714 (27/01/22)
        $input = $this->validate([
            'file' => 'uploaded[file]|max_size[file,2048]|ext_in[file,csv],'
        ]);

<<<<<<< HEAD
=======
        //Si il y a un fichier et un fichier non vide
>>>>>>> b2e8714 (27/01/22)
        if (!$input) 
        {
            //$data['validation'] = $this->validator;
            $messageErreur = "Erreur de lecture du fichier <br> vérifier son extension et son intégrité <br><br>";

            $data = [
                'messageErreur' => $messageErreur,
            ];

<<<<<<< HEAD
=======
            //retorune la vue d'acceuil 
>>>>>>> b2e8714 (27/01/22)
            return view('welcome_message', $data);
        }
        else
        {
<<<<<<< HEAD
            $file = $this->request->getFile('file');

            $namefile = strval($file->getName()) ;

            if( $file ) 
            {
                if( $file->isValid() && !$file->hasMoved() ) 
                {
                    
                    $newName = $file->getRandomName();

                    //déplacer le fichier 
                    $file->move(WRITEPATH.'../public/csvfile', $newName);

                    //file location
=======
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
>>>>>>> b2e8714 (27/01/22)
                    $filelocation = WRITEPATH."../public/csvfile/".$newName;

                    //get instance of file
                    $file = new \CodeIgniter\Files\File( $filelocation );

                    //ouvrir le fichier
<<<<<<< HEAD
                    //$file->openFile('r');
                    $fileopen = fopen( $file, "r" );

                    //première ligne du fichier
                    $line = fgets( $fileopen );

                    //tableau avec les champs de la 1ère ligne
                    $tabfields = explode( ";", $line );
=======
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
>>>>>>> b2e8714 (27/01/22)

                    //nb de fields
                    $numberOfFields = count( $tabfields );

                    //créer les colonnes

<<<<<<< HEAD
=======
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
>>>>>>> b2e8714 (27/01/22)
                    //création de l'id
                    /*
                    $fields = 
                    [
                        'id' => [ 'type' => 'INT', 'constraint' => 10, 'auto_increment' => true, 'unique' => true ]
                    ];

                    //ajout le field à la future table
                    $forge->addField( $fields );
                    */
<<<<<<< HEAD

                    for( $i = 0 ; $i < $numberOfFields ; $i++ )
                    {
                        if( strpos( $tabfields[$i] , "\n" ) !== false )
                        {
                            $tabfields[$i] = str_replace( "\n" , "" , $tabfields[$i] );
                        }
                        if( strpos( $tabfields[$i] , "\r" ) !== false )
                        {
                            $tabfields[$i] = str_replace( "\r" , "" , $tabfields[$i] );
                        }
                        $fields =
                        [
                            $tabfields[$i] => [ 'type' => 'VARCHAR', 'constraint' => 255 ]
                        ];

=======
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
>>>>>>> b2e8714 (27/01/22)
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

<<<<<<< HEAD
=======
                            //supprimer les caractères d'échappement
>>>>>>> b2e8714 (27/01/22)
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
<<<<<<< HEAD
                                $arr[$i] = explode( ";" , $buffer );
=======

                                //séparer les lignes en array en fonction du séparateur
                                $arr[$i] = explode( $separateur , $buffer );
>>>>>>> b2e8714 (27/01/22)

                                for( $z = 0 ; $z < $numberOfFields ; $z++ )
                                {
                                    $arr[$i][$tabfields[$z]] = $arr[$i][$z];
                                }

                                $arr[$i] = array_slice( $arr[$i], -$numberOfFields );
<<<<<<< HEAD
=======
                                //var_dump( $arr[$i] );
>>>>>>> b2e8714 (27/01/22)
                                
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

        $db = \Config\Database::connect();

        //tableau de toutes les tables
        $tables = $db->listTables();

        // Produces: SELECT * FROM mytable
        $builder = $db->table( $tables[0] );
        $sql = $builder->getCompiledSelect();
        $result = $db->query( $sql );
        $result = $result->getResult();

        //echo $result ;
        //var_dump( $result );

        $data = [
            'result' => "",
        ];

        echo view( 'update', $data );
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}
