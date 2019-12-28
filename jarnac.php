<?

// Script to test potential alternate scoring systems for the word game Jarnac (https://boardgamegeek.com/boardgame/7451/jarnac)

$matches = 0;
$maxdiff = 0;
$slice = 3; // tiles to set aside from each word before counting points
$tieslice = 0; // tiles to set aside from each word before determining a tiebreaker
$runs = 1000000;
$sqties = 0;

for ($i=0; $i<$runs; $i++)
{
	$firstgrid = array(rand(3,8),rand(3,8),rand(3,8),rand(3,8),rand(3,8),rand(3,8),rand(3,8),rand(3,8));
	$secondgrid = array(rand(3,8),rand(3,8),rand(3,8),rand(3,8),rand(3,8),rand(3,8),rand(3,8),0);

	$sqwinner = 0;
	if (sqscore($firstgrid)>sqscore($secondgrid)) { $sqwinner = 1; }
	else if (sqscore($firstgrid)<sqscore($secondgrid)) { $sqwinner = 2; }
	else { $sqties++; }

	$simwinner = 0;
	if (simscore($firstgrid)>simscore($secondgrid)) { $simwinner = 1; }
	else if (simscore($firstgrid)<simscore($secondgrid)) { $simwinner = 2; }
	else
	{
		// break tie in favour of player with most surviving word fragments
		$firstfrags = 0;
		foreach ($firstgrid as $word) { if ($word>$tieslice) { $firstfrags++; } }
		$secondfrags = 0;
		foreach ($secondgrid as $word) { if ($word>$tieslice) { $secondfrags++; } }
	
		if ($firstfrags > $secondfrags ) { $simwinner = 1; }
		if ($firstfrags < $secondfrags ) { $simwinner = 2; }
		
		// edit: always break tie in favour of the player who ended the game
		$simwinner = 1;
	}

	if ($simwinner == $sqwinner) { $matches++; }
	else
	{
		$diff = abs(sqscore($firstgrid)-sqscore($secondgrid));
		if ($diff>$maxdiff) { $maxdiff = $diff; }
//		print "mismatch of $diff: ".sqscore($firstgrid)."/".sqscore($secondgrid)." -> ".simscore($firstgrid)."/".simscore($secondgrid);
//		if (simscore($firstgrid)==simscore($secondgrid)) { print " - player $simwinner breaks tie"; }
//		print "\n";
	}
}

print "run with slice=$slice, tieslice=$tieslice: ".($matches/$runs*100)."% match, max difference on incorrect score = $maxdiff\n";
//print "\nthere were $sqties ties under the squared scoring";

function sqscore($grid)
{
	$score = 0;
	foreach ($grid as $word)
	{
		$score += $word*$word;
	}
	return $score;
}

function simscore($grid)
{
	global $slice;

	$score = 0;
	foreach ($grid as $word)
	{
		if ($word>$slice) { $score += $word-$slice; }
	}
	return $score;
}

?>
