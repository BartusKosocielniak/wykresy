<?php
//phpinfo();

include("hidden.php"); // require
$dsn = "mysql:dbname=$dbname;host=$host";
$dbh = new PDO($dsn, $user, $passwd);

//$data = $dbh->exec("select * from test");
//print_r($data);

// $stmt = $dbh->query("select * from test");
// while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
//   print_r($row);
// }


// select count(*) from users where login='".$_POST['user']"' and passwd='".$_POST['passwd']."'
// select count(*) from users where login='    ' or 1=1 --     ' and passwd='".$_POST['passwd']."'
// binduj!!!

$id=1; // w domysle wartosc od klienta (GET/POST)
$sth = $dbh->prepare("select * from temperature ORDER BY id");
// $sth->bindValue('id', $id, PDO::PARAM_INT);
$sth->execute();
$data = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
 print_r($row['id'] . " " . $row['temperature'] . " ");
}
echo '<pre>';
print_r($data[7]['id']);
echo '</pre>';
// print_r(count($data))
?>
