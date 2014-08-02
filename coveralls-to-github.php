<?php
echo 'Hello ' . htmlspecialchars($_POST["name"]) . '!';
$params = array('test' => 'Hello deedee !');

$r = new HttpRequest('http://requestb.in/o18egeo1', HttpRequest::METH_POST);
$r->setOptions(array('redirect' => 10));
$r->setContentType ('application/json');
$r->setBody(json_encode(params));

try {
    $r->send();
    if ($r->getResponseCode() == 200) {
        //file_put_contents('local.rss', $r->getResponseBody());
    }
} catch (HttpException $ex) {
    echo $ex;
}
?>
