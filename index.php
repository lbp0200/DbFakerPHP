<?php
/**
 * Created by PhpStorm.
 * User: bopingliu
 * Date: 2016/4/7
 * Time: 13:44
 */
require 'vendor/autoload.php';

$dbPath = '/home/lbp/tmp/person1.sqlite3';
$pdo = new PDO("sqlite:{$dbPath}");
$fpdo = new FluentPDO($pdo);

$qryTbl = $fpdo->from('sqlite_master')->where('type', '=', 'table')->where('name', '=', 'person');
$tblName = $qryTbl->fetch('name');
var_dump($tblName);
if (!$tblName) {
    $pdo->exec('CREATE TABLE person
(
    id integer PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    country TEXT,
    state TEXT,
    city TEXT,
    address TEXT,
    postcode TEXT,
    latitude REAL,
    longitude REAL,
    phoneNumber TEXT,
    birthday DATE,
    email TEXT
)');
}

$faker = Faker\Factory::create();

for ($j = 0; $j < 10000; $j++) {
    $rows = array();
    for ($i = 0; $i < 500; $i++) {
        array_push($rows, array(
//            $faker->uuid,
            $faker->name,
            $faker->country,
            $faker->state,

            $faker->city,
            $faker->address,
            $faker->postcode,
            $faker->latitude,

            $faker->longitude,
            $faker->phoneNumber,
            $faker->date(),
            $faker->email,
        ));
    }
    insertBlockData($pdo, $rows);
}

$query = $fpdo->from('person')
    ->limit(5);
foreach ($query as $row) {
    var_dump($row);
}

function insertBlockData($dbh, $rows)
{
    $t1 = microtime(true);
    $params = array();
    $query = "INSERT INTO person (name, country, state, city, address, postcode, latitude, longitude, phoneNumber, birthday, email) VALUES ";
    foreach ($rows as $row) {
        $query .= "(?,?,?,?,?,?,?,?,?,?,?),";
        foreach ($row as $value) {
            $params[] = $value;
        }
    }
    $query = substr($query, 0, -1);
    $stmt = $dbh->prepare($query);
    if (!$stmt) {
        echo "\nPDO::errorInfo():\n";
        print_r($dbh->errorInfo());
    }
    $r = $stmt->execute($params);
    $t2 = microtime(true);
    echo (($t2 - $t1) * 1000) . 'ms' . PHP_EOL;
}