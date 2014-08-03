<?php
$GITHUB_API_TOKEN = '%GITHUB_API_TOKEN%';

$repo = $_POST['repo_name'];
$commit = $_POST['commit_sha'];
$branch = $_POST['branch'];
$coverageChange = $_POST['coverage_change'];

// Retrive previous comment
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/'.$repo.'/commits/'.$commit.'/comments');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: coveralls-to-github', 'Authorization: token '.$GITHUB_API_TOKEN));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$comments=curl_exec($ch);
if(curl_errno($ch)){echo 'ERREUR GET curl_exec : '.curl_error($ch);}
if(curl_getinfo($ch, CURLINFO_HTTP_CODE)!=200){echo 'HTTP GET code : '.curl_getinfo($ch, CURLINFO_HTTP_CODE).' received';die();}
curl_close($ch);

$comments = json_decode($comments);

$commentId = -1;
foreach($comments as $comment) {
    if (strpos($comment->body, '[Coverage Status]') !== false) {
        $commentId = $comment->id;
    }
    echo $comment->body;
}
 
$message = '[![Coverage Status](https://coveralls.io/repos/'.$repo.'/badge.png?branch='.$branch.')](https://coveralls.io/r/'.$repo.'?branch='.$branch.') ';
$covChange = floatval($coverageChange);
if ($covChange > -0.01 && $covChange < 0.01)
    $message .= '+0.0% :relieved:';
else if ($covChange >= 0.01) 
    $message .= '+'.$covChange.'% :kissing_heart:';
else
    $message .= '-'.$covChange.'% :scream:';

//Coverage increased (+0.0%) when pulling a3264ae on eush77:bellman-ford-early-stop into a2d30b0 on felipernb:master.
// Send comment
$ch = curl_init();

if ($commentId <= 0) {
    curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/'.$repo.'/commits/'.$commit.'/comments' );
    curl_setopt($ch, CURLOPT_POST, 1 );
} else {
    curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/'.$repo.'/comments/'.$commentId );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH' );
}
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('body' => $message)) ); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: coveralls-to-github', 'Content-Type: application/json', 'Authorization: token '.$GITHUB_API_TOKEN)); 

$result=curl_exec($ch);
if(curl_errno($ch)){echo 'ERREUR POST curl_exec : '.curl_error($ch);}
if(curl_getinfo($ch, CURLINFO_HTTP_CODE)!=200 || curl_getinfo($ch, CURLINFO_HTTP_CODE)!=201){echo 'HTTP POST code : '.curl_getinfo($ch, CURLINFO_HTTP_CODE).' received';die();}
curl_close($ch);
?>
