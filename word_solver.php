<?php
function read_grid($file)
{
    $string = file_get_contents($file);
    $grid = explode("\n", $string);
    $grid_final;

    for ($i=0; $i < count($grid); $i++)
    {
        for ($j=0; $j <  strlen($grid[0]); $j++)
        {
            $grid_final[$i][$j] = $grid[$i]{$j};
        }

    }
    return $grid_final;
}

function read_words()
{
    while ($word = fgets(STDIN))
    {
        $word = trim($word, "\n");
        $word = strtoupper($word);
        $words[] = $word;
    }

    if (strlen($words[0]) == 0)
    {
        echo "Veuillez entrer des mots à rechercher.\n";
        exit;
    }
    else
    {
        return $words;
    }
}

function find_first_letter($words, $grid)
{
    foreach ($words as $word)
        {
        $lengthW = strlen($word);
        foreach ($grid as $nbLine => $line)
        {
            foreach ($line as $nbChar => $char)
            {
                $lengthL = count($line);
                if ($word{0} == $char)
                {
                    $direction_match[] = find_following_letters($nbLine, $nbChar, $grid, $word, $lengthW, $lengthL, $char);
                }
            }
        }
    }
    return clean_match($direction_match, $words);
}

function find_following_letters($nbLine, $nbChar, $grid, $word, $lengthW, $lengthL, $char)
{
    $possibility = "";

    if ($nbChar >= $lengthW - 1)
        $possibility .= "O";
    if (($lengthL - $nbChar) >= $lengthW)
        $possibility .= "E";
    if ($nbLine >= $lengthW - 1)
        $possibility .= "N";
    if ((count($grid) - $nbLine) > $lengthW - 1)
        $possibility .= "S";
    if (preg_match("/N/", $possibility) && preg_match("/O/", $possibility))
        $possibility .= "I";
    if (preg_match("/N/", $possibility) && preg_match("/E/", $possibility))
        $possibility .= "J";
    if (preg_match("/S/", $possibility) && preg_match("/O/", $possibility))
        $possibility .= "K";
    if (preg_match("/S/", $possibility) && preg_match("/E/", $possibility))
        $possibility .= "L";

    $i = 1;
    $direction_match = array(
        "Word" => $word,
        "Coord" => "$nbChar,$nbLine",
        "O" => 0,
        "E" => 0,
        "N" => 0,
        "S" => 0,
        "I" => 0,
        "J" => 0,
        "K" => 0,
        "L" => 0);

    while ($i < $lengthW)
    {
        if (preg_match("/O/", $possibility))
        {
            if ($grid[$nbLine][$nbChar - $i] == $word{$i})
                $direction_match['O']++;
        }
        if (preg_match("/E/", $possibility))
        {
            if ($grid[$nbLine][$nbChar + $i] == $word{$i})
                $direction_match['E']++;
        }
        if (preg_match("/N/", $possibility))
        {
            if ($grid[$nbLine - $i][$nbChar] == $word{$i})
                $direction_match['N']++;
        }
        if (preg_match("/S/", $possibility))
        {
            if ($grid[$nbLine + $i][$nbChar] == $word{$i})
                $direction_match['S']++;
        }
        if (preg_match("/I/", $possibility))
        {
            if ($grid[$nbLine - $i][$nbChar - $i] == $word{$i})
                $direction_match['I']++;
        }
        if (preg_match("/J/", $possibility))
        {
            if ($grid[$nbLine - $i][$nbChar + $i] == $word{$i})
                $direction_match['J']++;
        }
        if (preg_match("/K/", $possibility))
        {
            if ($grid[$nbLine + $i][$nbChar - $i] == $word{$i})
                $direction_match['K']++;
        }
        if (preg_match("/L/", $possibility))
        {
            if ($grid[$nbLine + $i][$nbChar + $i] == $word{$i})
                $direction_match['L']++;
        }
        $i++;
    }
    return $direction_match;
}

function clean_match($direction_match, $words)
{
    foreach ($words as $word)
    {
        foreach ($direction_match as $key => $matches)
        {
            $lengthW = strlen($word) - 1;
            $compteur = 0;

            foreach ($matches as $direction => $hit)
            {
                if ($matches['Word'] == $word && is_int($hit) && $hit <  $lengthW)
                {
                    $compteur++;
                }
            }
            if ($compteur == 8)
            {
                unset($direction_match[$key]);
            }
        }
    }

    foreach ($words as $word)
    {
        foreach ($direction_match as $key => $value)
        {
            foreach ($value as $key1 => $value2)
            {
                $lengthW = strlen($word - 1);
                if (is_int($value2) && $value2 < $lengthW)
                {
                    unset($direction_match[$key][$key1]);
                }
            }
        }
    }
    return $direction_match;
}

function print_grid($grid, $direction_match)
{
    foreach ($direction_match as $key => $matches)
    {
        $coord = explode(",", $direction_match[$key]['Coord']);
        $x = $coord[0];
        $y = $coord[1];
        $i = 1;

        $grid[$y][$x] = yellow($grid[$y][$x]);

        switch (key(array_reverse($matches)))
        {
            case 'O':
                for ($i=1; $i <= $matches['O']; $i++) 
                    $grid[$y][$x - $i]  = yellow($grid[$y][$x - $i]);
                break;
            case 'E':
                for ($i=1; $i <= $matches['E']; $i++) 
                    $grid[$y][$x + $i]  = yellow($grid[$y][$x + $i]);
                break;
            case 'N':
                for ($i=0; $i <= $matches['N']; $i++) 
                    $grid[$y - $i][$x]  = yellow($grid[$y - $i][$x]);
                break;
            case 'S':
                for ($i=0; $i <= $matches['S']; $i++) 
                    $grid[$y + $i][$x]  = yellow($grid[$y + $i][$x]);
                break;
            case 'I':
                for ($i=0; $i <= $matches['I']; $i++) 
                    $grid[$y - $i][$x - $i] = yellow($grid[$y - $i][$x - $i]);
                break;
            case 'J':
                for ($i=0; $i <= $matches['J']; $i++) 
                    $grid[$y - $i][$x + $i] = yellow($grid[$y - $i][$x + $i]);
                break;
            case 'K':
                for ($i=0; $i <= $matches['K']; $i++) 
                    $grid[$y + $i][$x - $i] = yellow($grid[$y + $i][$x - $i]);
                break;
            case 'L':
                for ($i=0; $i <= $matches['L']; $i++) 
                    $grid[$y + $i][$x + $i] = yellow($grid[$y + $i][$x + $i]);
                break;
        }
    }

    foreach ($grid as $key => $line)
    {
        foreach ($line as $key => $char)
        {
            echo $char;
        }
        echo "\n";
    }
    exit(count($direction_match));
}

function yellow($char)
{
    return chr(27) . "[43m" . $char . chr(27) . "[0m";
}

unset($argv[0]);
if (count($argv) < 1)
{
    echo "Veuillez fournir au moins 1 grille.\n";
    return false;
}

foreach ($argv as $file)
{
    if (file_exists($file))
    {
        $grid = read_grid($file);
        $words = read_words();
        $direction_match = find_first_letter($words, $grid);
        print_grid($grid, $direction_match);
    }
    else
    {
        echo "Le fichier $file n'existe pas !\n";
        return false;
    }
}
?>