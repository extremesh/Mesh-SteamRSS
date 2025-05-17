<?php
/*
 * --- ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
 * --- ► Mesh-SteamRSS v1.0 -  A RSS/XML Feed for your Steam Gaming Activity
 * --- ► made by Ramesh Hetfield (eXtreMesh)
 * --- ► project page: https://meshra.net/?p=270
 * --- ► follow on BlueSky: https://ramesh.my/?bluesky
 * --- ► on Discord: https://ramesh.my/?discord 
 * --- ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
 */

header('Content-Type: application/rss+xml; charset=utf-8');

//---------- Please input your SteamID64 & Steam Web Api Key here
//---------- #
$apiKey = 'your-api-key-here'; //Your Steam Web Api Key here.
$steamId = 'your-steamID64-here'; //Your SteamID64 here.


//---------- Thats it, no need to change anything below this.
//---------- #
$recentGamesUrl = "https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v1/?key=$apiKey&steamid=$steamId";
$recentData = json_decode(file_get_contents($recentGamesUrl), true);

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rss version="2.0">
  <channel>
    <title>My Steam Gaming Activity Feed</title>
    <link>https://steamcommunity.com/profiles/<?php echo $steamId; ?>/</link>
    <description>Recent Games Played in the Past 2 Weeks.</description>
<?php
if (isset($recentData['response']['games'])) {

    foreach ($recentData['response']['games'] as $game) {
        $appid = $game['appid'];
        $name = htmlspecialchars($game['name'], ENT_QUOTES | ENT_XML1, 'UTF-8');
        $playtime = round($game['playtime_2weeks'] / 60, 2); // in hours

        // Fetch achievements
        $achievementsUrl = "https://api.steampowered.com/ISteamUserStats/GetPlayerAchievements/v1/?key=$apiKey&steamid=$steamId&appid=$appid";
        $achievementsData = @json_decode(file_get_contents($achievementsUrl), true);
        $achievedCount = 0;

        if (isset($achievementsData['playerstats']['achievements'])) {
            foreach ($achievementsData['playerstats']['achievements'] as $ach) {
                if ($ach['achieved'] == 1) {
                    $achievedCount++;
                }
            }
        }

        $description = "Played for {$playtime} hrs (in the last 2 weeks) with {$achievedCount} Achievements unlocked so far.";
        $descriptionEscaped = htmlspecialchars($description, ENT_QUOTES | ENT_XML1, 'UTF-8');

        echo "    <item>\n";
        echo "      <title>$name</title>\n";
        echo "      <link>https://store.steampowered.com/app/$appid/</link>\n";
        echo "      <description>$descriptionEscaped</description>\n";
        echo "      <guid isPermaLink=\"false\">$appid</guid>\n";
        echo "      <pubDate>" . date(DATE_RSS) . "</pubDate>\n";
        echo "    </item>\n";
    }
}
?>
  </channel>
</rss>

<?php
//---------- End of code! - Ramesh Hetfield
?>

