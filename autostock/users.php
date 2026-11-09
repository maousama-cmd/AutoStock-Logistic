<CodeBlock language="php" editable>
<?php
require_once __DIR__ . '/config/database.php';


$akun = [
[
'nama' => 'Administrator',
'username' => 'admin',
'password' => 'admin123',
'role' => 'admin'
],
[
'nama' => 'Petugas Gudang',
'username' => 'petugas1',
'password' => 'admin123',
'role' => 'petugas'
]
];

$sql = "INSERT IGNORE INTO users (nama, username, password, role)
VALUES (:nama, :username, :password, :role)";

$stmt = $db->prepare($sql);

foreach ($akun as $data) {
$stmt->execute([
':nama' => $data['nama'],
':username' => $data['username'],
':password' => password_hash($data['password'], PASSWORD_BCRYPT),
':role' => $data['role']
]);
}

echo "Akun petugas berhasil disiapkan!";
?>
</CodeBlock>