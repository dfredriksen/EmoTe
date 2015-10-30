<?php

    ini_set('include_path', '/home/dfredriksen/EmoTe' . ini_get('include_path'));
    require_once('/home/dfredriksen/EmoTe/Eklekt/Emotion.php');
    require_once('/home/dfredriksen/EmoTe/Eklekt/Emotion/Empathyscope.php');
    require_once('/home/dfredriksen/EmoTe/Eklekt/Emotion/EmotionalState.php');
    require_once('/home/dfredriksen/EmoTe/Eklekt/Emotion/AffectWord.php');

    require_once('/home/dfredriksen/EmoTe/Eklekt/Emotion/Utility/Heuristics.php');
    require_once('/home/dfredriksen/EmoTe/Eklekt/Emotion/Utility/Lexical.php');


    $configs = parse_ini_file('config.ini');
    
    $host = $configs['database'];
    $user = $configs['user'];
    $pass = $configs['password'];
    $db = new PDO("mysql:host=$host;dbname=Analysis;charset=utf8", $user, $pass);
    $result = $db->query('SELECT row_id, neutral, happy, sad, fear, anger, disgust, surprise from Random');
    while( $row = $result->fetch(PDO::FETCH_ASSOC))
    {
        $id = $row['row_id'];

        $emotions = array(
            '0' => $row['neutral'],
            '1' => $row['happy'],
            '2' => $row['sad'],
            '3' => $row['fear'],
            '4' => $row['anger'],
            '5' => $row['disgust'],
            '6' => $row['surprise']
        );

        $winner = -1;
        $weight = -1;

        foreach($emotions as $emotion=>$value) 
        {
            if($value > $weight) {
                $winner = $emotion;
                $weight = $value;
            } else if ( $value == $weight ) {
                $winner = 7;
                break;
            }
        }

        echo "Row $id is a $winner with a weight of $weight.\n";
        $db->exec("UPDATE Random set primary_emotion=$winner where row_id=$id");
    }

 echo "Done!";
