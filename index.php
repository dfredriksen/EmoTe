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
    $Empathyscope = Eklekt_Emotion_Empathyscope::getInstance();
    $db = new PDO("mysql:host=$host;dbname=Analysis;charset=utf8", $user, $pass);
    $result = $db->query('SELECT row_id, headline, teaser from Random');
    while( $row = $result->fetch(PDO::FETCH_ASSOC))
    {
        $emotions = array(
            '-1' => 0,
            '0' => 0,
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0
        );
        $id = $row['row_id'];
        $headline = $row['headline'];
        $teaser = strip_tags(html_entity_decode($row['teaser'], ENT_QUOTES));
        $text = $headline . ". ". $teaser;
        $analysis = $Empathyscope->feel($text);
        $valence = $analysis->valence;

        foreach( $analysis->emotions as $emotion ) {
            $emotions[$emotion->type] = $emotion->weight;
        }

        $db->exec("UPDATE Random set 
            neutral=" . $emotions['-1'] .",
            happy=" . $emotions[0] . ",
            sad=" . $emotions[1] . ",
            fear=" . $emotions[2] . ",
            anger=" . $emotions[3] . ",
            disgust=" . $emotions[4] . ",
            surprise=" . $emotions[5] . ",
            valence=$valence
            WHERE row_id=$id");

        echo "Analyzing row $id\n..";
    }

 echo "Done!";
