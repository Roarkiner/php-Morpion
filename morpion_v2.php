<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Petit jeu de morpion</title>
</head>
    <body>
        <?php
            //Fonction pour sauter deux lignes

            function jumpTwoLines(){
                echo '<br><br>';
            }

            //Fonction pour connaître le joueur, si innitialisation du plateau, joueur aléatoire

            function getPlayer(){
                if(!isset($_GET['player'])){
                    $firstPlaying = rand(0, 1);
                    if($firstPlaying === 0){
                        return 'X';
                    } else {
                        return 'O';   
                    }
                } else {
                    return $_GET['player'];
                }
            }

            //Fonction pour déterminer quel est le prochain joueur

            function nextPlayer($currentPlayer){
                if($currentPlayer === 'X'){
                    return 'O';
                } else {
                    return 'X';
                }
            }

            //Fonction pour afficher le joueur

            function echoPlayer($player){
                echo '<h1> Au tour du joueur des ' . $player . '</h1>';
            }

            //Fonction qui teste toutes les lignes. Si une est complète avec la même valeur (autre que 0), renvoie l'index de la ligne, sinon renvoie false

            function testRows($tab){
                foreach($tab as $trIndex => $tr){
                    $firstValue = $tr[0];
                    $rowflag = true;
            
                    foreach($tr as $td){
                        if($td !== $firstValue || $td === '0'){
                            $rowflag = false;
                        }
                    }
                    if($rowflag){
                        return $trIndex;
                    }
                }
                return false;
            }

            //Fonction qui teste toutes les colonnes. Si une est complète avec la même valeur (autre que 0), renvoie l'index de la colonne, sinon renvoie false

            function testColumns($tab){
                $firstRow = $tab[0];
                $columnsFlag = [];
            
                foreach($tab as $trIndex => $tr){
                    foreach($tr as $tdIndex => $td){
                        if($trIndex === 0){
                            array_push($columnsFlag, true);
                        } else if($td !== $firstRow[$tdIndex] || $td === '0'){
                            $columnsFlag[$tdIndex] = false;
                        }
                    }
                }
            
                foreach($columnsFlag as $index => $bool){
                    if($bool){
                        return $index;
                    }
                }
                return false;
            }

            //Fonction qui teste la diagonale descendante, renvoie true si elle est remplie de la même valeur (autre que 0) sinon renvoie false

            function testDownwardDiag($tab){
                $downwardDiagFlag = true;
                $firstValue = $tab[0][0];

                foreach($tab as $trIndex => $tr){
                    foreach($tr as $tdIndex => $td){
                        if($trIndex === $tdIndex && ($td !== $firstValue || $td === '0')){
                            $downwardDiagFlag = false;
                        }
                    }
                }
                return $downwardDiagFlag;
            }

            //Fonction qui teste la diagonale montante, renvoie true si elle est remplie de la même valeur (autre que 0) sinon renvoie false

            function testUpwardDiag($tab){
                $upwardDiagFlag = true;
                $lastTdIndex = count($tab) - 1;
                $firstValue = $tab[0][$lastTdIndex];
            
                foreach($tab as $trIndex => $tr){
                    foreach($tr as $tdIndex => $td){
                        if($trIndex + $tdIndex === $lastTdIndex && ($td !== $firstValue || $td === '0')){
                            $upwardDiagFlag = false;
                        }
                    }
                }
                return $upwardDiagFlag;
            }

            function testDraw($tab){
                $drawFlag = true;

                foreach($tab as $tr){
                    foreach($tr as $td){
                        if($td === '0'){
                            $drawFlag = false;
                        }
                    }
                }
                return $drawFlag;
            }

            /*
            Fonction qui retourne sous forme de tableau si la partie est finie :
                - Qui a gagné ('X' ou 'O')
                - Si oui dans quelle configuration (sur les 9 cases) ex : return['X', 6, 7, 8]; => 3 x sur la 3ème ligne
                - Sinon égalité ('draw')
                - Sinon retourne false
            */

            function endGame($tab){
                $tabSize = count($tab);
                
                $resultRows = testRows($tab);
                $resultColumns = testColumns($tab);
                $resultDownwardDiag = testDownwardDiag($tab);
                $resultUpwardDiag = testUpwardDiag($tab);

                $results = array_filter([
                    'resultRows' => $resultRows,
                    'resultColumns' => $resultColumns,
                    'resultDownwardDiag' => $resultDownwardDiag,                
                    'resultUpwardDiag' => $resultUpwardDiag
                ], function ($var){
                    return ($var !== false);
                });


                if(empty($results)){
                    if(testDraw($tab)){
                        return ['draw'];
                    } else {
                        return false;
                    }
                }
                
                $resultsToReturn = [];

                for( $i = 0 ; $i < $tabSize ; $i++ ){
                    if(isset($results['resultRows'])) array_push($resultsToReturn, $results['resultRows'] * $tabSize + $i);
                    if(isset($results['resultColumns'])) array_push($resultsToReturn, $results['resultColumns'] + $i * $tabSize);
                    if(isset($results['resultDownwardDiag'])) array_push($resultsToReturn, $tabSize * $i + $i);
                    if(isset($results['resultUpwardDiag'])) array_push($resultsToReturn, ($tabSize - 1) * ($i + 1));
                }
            
                $firstColumnIndex = $resultsToReturn[0] % $tabSize;
                $firstRowIndex = ($resultsToReturn[0] - $firstColumnIndex) / $tabSize;
                array_unshift($resultsToReturn, $tab[$firstRowIndex][$firstColumnIndex]);
                
                return array_unique($resultsToReturn);
            }

            //Fonction pour écrire le résultat

            function echoEndResults($results){
                if($results[0] === 'draw'){
                    echo '<h2>La partie se termine sur une égalité !</h2>';
                } else {
                    echo '<h2>' . $results[0] . ' gagne!</h2>';
                }
                echo '<a href="index.html" id="start_button">Rejouer ?</a>';
            }

            //Fonction pour créer les liens des cases vides

            function linkCreation($trIndex, $tdIndex, $player, $tab, $tabName){
                $link = '<a class="empty" href="morpion_v2.php?';

                foreach($tab as $tabTrIndex => $tr){
                    foreach($tr as $tabTdIndex => $td){
                        if($tabTrIndex === $trIndex && $tabTdIndex === $tdIndex){
                            $link .= $tabName[$tabTrIndex][$tabTdIndex] . $player;        
                        } else {
                            $link .= $tabName[$tabTrIndex][$tabTdIndex] . $tab[$tabTrIndex][$tabTdIndex];
                        }
                    }
                }

                $link .= '&player=' . nextPlayer($player) . '">';
                echo $link;
            }

            //Fonction pour définir la classe des td du tableau

            function tdType($cell, $tdNum, $gameOver){
                $class = '';

                switch ($cell){
                    case 'X':
                        $class = 'xPlayer';
                        break;
                    case 'O':
                        $class = 'oPlayer';
                        break;
                }

                if(isset($gameOver[1])){
                    foreach($gameOver as $winningCell){
                        if($tdNum === $winningCell){
                            $class .= ' winning_cell';
                        }
                    }
                }
                return $class;
            }

            //Fonction pour écrire le tableau

            function writeMorpionCells($tab, $tabName, $end, $player){
                echo '<table>';
                $tabSize = count($tab);

                foreach($tab as $trIndex => $tr){
                    foreach($tr as $tdIndex => $td){
            
                        $tdNum = $trIndex * $tabSize + $tdIndex;
            
                        $openningTrCase = ($tdIndex === 0);
                        $closingTrCase = ($tdIndex === $tabSize - 1);
            
                        if($openningTrCase){
                            echo '<tr>';
                        }
                        
                        $classType = tdType($td, $tdNum, $end);
                        
                        echo '<td class="' . $classType . '">';
            
                        //Créatin des liens dans les cases vides
            
                        if($td === '0' && !$end){
                            echo linkCreation($trIndex, $tdIndex, $player, $tab, $tabName);
                        }
            
                        //Ecrire dans les cases
                        if($td !== '0'){
                            echo($td);
                        }
            
                        if($td === '0' && !$end){
                            echo '</a>';
                        }
            
                        echo '</td>';
            
                        if($closingTrCase) {
                            echo '</tr>';
                        }
                    }
                }
                echo '</table>';
            }

            //Définition de chaque case
            
            //0 = rien, Joueur X, Joueur O 

            $cellules = [
                [ $_GET['uLeft'], $_GET['uMid'], $_GET['uRight'] ],
                [ $_GET['mLeft'], $_GET['mMid'], $_GET['mRight'] ],
                [ $_GET['dLeft'], $_GET['dMid'], $_GET['dRight'] ]
            ];

            $cellulesName = [
                ['uLeft=', '&uMid=', '&uRight='],
                ['&mLeft=', '&mMid=', '&mRight='],
                ['&dLeft=', '&dMid=', '&dRight=']
            ];

            //Définition des variables utiles puis appel des fonctions

            $gameOver = endGame($cellules);

            $player = getPlayer();

            if(!$gameOver){
                echoPlayer($player);
            }

            writeMorpionCells($cellules, $cellulesName, $gameOver, $player);

            if($gameOver !== false){
                echoEndResults($gameOver);
            }
        ?>
    </body>
</html>